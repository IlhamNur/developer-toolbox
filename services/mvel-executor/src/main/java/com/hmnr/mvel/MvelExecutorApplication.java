package com.hmnr.mvel;

import com.fasterxml.jackson.core.JsonProcessingException;
import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;
import com.fasterxml.jackson.databind.node.ArrayNode;
import com.fasterxml.jackson.databind.node.ObjectNode;
import com.sun.net.httpserver.HttpExchange;
import com.sun.net.httpserver.HttpServer;
import org.mvel2.MVEL;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.InetSocketAddress;
import java.nio.charset.StandardCharsets;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;
import java.util.Set;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;
import java.util.concurrent.Future;
import java.util.concurrent.TimeUnit;
import java.util.concurrent.TimeoutException;
import java.util.regex.Pattern;

public final class MvelExecutorApplication {
    static final int MAX_EXPRESSION_BYTES = 50 * 1024;
    static final int MAX_VARIABLES = 100;
    static final int MAX_RESULT_BYTES = 1024 * 1024;
    static final long TIMEOUT_MS = 1000;
    static final String MVEL_VERSION = "2.5.2.Final";
    private static final ObjectMapper JSON = new ObjectMapper();
    private static final ExecutorService EXECUTOR = Executors.newCachedThreadPool();
    private static final Pattern DANGEROUS = Pattern.compile(
        "(?i)(java\\.lang|java\\.io|java\\.net|java\\.nio|java\\.reflect|classloader|runtime|processbuilder|system\\.|getclass|forname|\\.class|new\\s+)"
    );

    private MvelExecutorApplication() {
    }

    public static void main(String[] args) throws IOException {
        int port = Integer.parseInt(System.getenv().getOrDefault("MVEL_EXECUTOR_PORT", "8081"));
        HttpServer server = HttpServer.create(new InetSocketAddress("0.0.0.0", port), 0);
        server.createContext("/api/mvel/health", MvelExecutorApplication::health);
        server.createContext("/api/mvel/execute", MvelExecutorApplication::execute);
        server.setExecutor(Executors.newFixedThreadPool(4));
        server.start();
    }

    private static void health(HttpExchange exchange) throws IOException {
        if (!"GET".equalsIgnoreCase(exchange.getRequestMethod())) {
            respond(exchange, 405, error("METHOD_NOT_ALLOWED", "Only GET is supported."));
            return;
        }

        ObjectNode response = JSON.createObjectNode();
        response.put("status", "UP");
        response.put("mvelVersion", MVEL_VERSION);
        respond(exchange, 200, response);
    }

    private static void execute(HttpExchange exchange) throws IOException {
        if (!"POST".equalsIgnoreCase(exchange.getRequestMethod())) {
            respond(exchange, 405, error("METHOD_NOT_ALLOWED", "Only POST is supported."));
            return;
        }

        try {
            JsonNode request = JSON.readTree(readLimited(exchange.getRequestBody(), 2 * 1024 * 1024));
            String expression = request.path("expression").asText("");
            JsonNode variablesNode = request.path("variables");

            validate(expression, variablesNode);
            Map<String, Object> variables = JSON.convertValue(variablesNode, Map.class);
            Future<Object> future = EXECUTOR.submit(() -> MVEL.eval(expression, new HashMap<>(variables)));
            long started = System.nanoTime();
            Object result;

            try {
                result = future.get(TIMEOUT_MS, TimeUnit.MILLISECONDS);
            } catch (TimeoutException exception) {
                future.cancel(true);
                respond(exchange, 408, error("MVEL_TIMEOUT", "The expression exceeded the allowed execution time."));
                return;
            }

            JsonNode safeResult = safeResult(result, 0);
            if (JSON.writeValueAsBytes(safeResult).length > MAX_RESULT_BYTES) {
                respond(exchange, 413, error("RESULT_TOO_LARGE", "The result exceeded the allowed size."));
                return;
            }

            ObjectNode response = JSON.createObjectNode();
            response.put("success", true);
            response.set("result", safeResult);
            response.put("type", result == null ? "null" : result.getClass().getName());
            response.put("executionTimeMs", Math.round((System.nanoTime() - started) / 10000.0) / 100.0);
            respond(exchange, 200, response);
        } catch (MvelSecurityException exception) {
            respond(exchange, 400, error("DANGEROUS_EXPRESSION", exception.getMessage()));
        } catch (IllegalArgumentException exception) {
            respond(exchange, 400, error("INVALID_REQUEST", exception.getMessage()));
        } catch (Exception exception) {
            respond(exchange, 422, error("MVEL_EXECUTION_ERROR", sanitize(exception)));
        }
    }

    private static void validate(String expression, JsonNode variables) {
        if (expression.isEmpty() || expression.getBytes(StandardCharsets.UTF_8).length > MAX_EXPRESSION_BYTES) {
            throw new IllegalArgumentException("Expression is required and must be at most 50 KB.");
        }
        if (!variables.isObject() || variables.size() > MAX_VARIABLES) {
            throw new IllegalArgumentException("Variables must be an object with at most 100 entries.");
        }
        if (DANGEROUS.matcher(expression).find()) {
            throw new MvelSecurityException("This expression uses a restricted Java capability.");
        }
    }

    private static JsonNode safeResult(Object value, int depth) {
        if (depth > 20 || value == null || value instanceof String || value instanceof Number || value instanceof Boolean) {
            return JSON.valueToTree(value);
        }
        if (value instanceof Map) {
            ObjectNode object = JSON.createObjectNode();
            for (Map.Entry<?, ?> entry : ((Map<?, ?>) value).entrySet()) {
                if (entry.getKey() != null) {
                    object.set(String.valueOf(entry.getKey()), safeResult(entry.getValue(), depth + 1));
                }
            }
            return object;
        }
        if (value instanceof Iterable) {
            ArrayNode array = JSON.createArrayNode();
            for (Object item : (Iterable<?>) value) {
                array.add(safeResult(item, depth + 1));
            }
            return array;
        }
        throw new MvelSecurityException("The result type cannot be safely serialized.");
    }

    private static byte[] readLimited(InputStream input, int limit) throws IOException {
        byte[] buffer = input.readAllBytes();
        if (buffer.length > limit) {
            throw new IllegalArgumentException("Request is too large.");
        }
        return buffer;
    }

    private static String sanitize(Exception exception) {
        String message = exception.getMessage();
        return message == null || message.isBlank() ? "The expression could not be executed." : message.split("\n", 2)[0];
    }

    private static ObjectNode error(String type, String message) {
        ObjectNode response = JSON.createObjectNode();
        response.put("success", false);
        ObjectNode error = response.putObject("error");
        error.put("type", type);
        error.put("message", message);
        return response;
    }

    private static void respond(HttpExchange exchange, int status, JsonNode body) throws IOException {
        byte[] payload = JSON.writeValueAsBytes(body);
        exchange.getResponseHeaders().set("Content-Type", "application/json");
        exchange.getResponseHeaders().set("Access-Control-Allow-Origin", "*");
        exchange.sendResponseHeaders(status, payload.length);
        try (OutputStream output = exchange.getResponseBody()) {
            output.write(payload);
        }
    }

    static final class MvelSecurityException extends RuntimeException {
        MvelSecurityException(String message) {
            super(message);
        }
    }
}
