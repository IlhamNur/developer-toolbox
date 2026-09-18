package com.hmnr.mvel;

import com.fasterxml.jackson.databind.JsonNode;
import com.fasterxml.jackson.databind.ObjectMapper;
import org.junit.jupiter.api.Test;
import org.mvel2.MVEL;

import java.util.HashMap;
import java.util.Map;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertThrows;

class MvelExecutorApplicationTest {
    private final ObjectMapper json = new ObjectMapper();

    @Test
    void evaluatesStringExpressionWithVariable() {
        Map<String, Object> variables = new HashMap<>();
        variables.put("firstName", "Ilham");

        assertEquals("Hello Ilham", MVEL.eval("\"Hello \" + firstName", variables));
    }

    @Test
    void evaluatesNestedMapsAndLists() {
        Map<String, Object> response = new HashMap<>();
        response.put("success", true);
        response.put("data", Map.of("ticketNumber", "SRN123456"));
        Map<String, Object> variables = Map.of("response", response, "items", java.util.List.of("Laravel", "MVEL"));

        assertEquals("SRN123456", MVEL.eval("response.data.ticketNumber", variables));
        assertEquals("Laravel", MVEL.eval("items[0]", variables));
    }

    @Test
    void rejectsDangerousExpressionPattern() {
        assertThrows(MvelExecutorApplication.MvelSecurityException.class, () ->
            invokeValidation("Runtime.getRuntime().exec('id')", "{}"));
    }

    @Test
    void rejectsOversizedExpression() {
        assertThrows(IllegalArgumentException.class, () ->
            invokeValidation("x".repeat(MvelExecutorApplication.MAX_EXPRESSION_BYTES + 1), "{}"));
    }

    private void invokeValidation(String expression, String variables) throws Exception {
        JsonNode variablesNode = json.readTree(variables);
        var method = MvelExecutorApplication.class.getDeclaredMethod("validate", String.class, JsonNode.class);
        method.setAccessible(true);
        try {
            method.invoke(null, expression, variablesNode);
        } catch (java.lang.reflect.InvocationTargetException exception) {
            if (exception.getCause() instanceof RuntimeException) {
                throw (RuntimeException) exception.getCause();
            }
            throw exception;
        }
    }
}
