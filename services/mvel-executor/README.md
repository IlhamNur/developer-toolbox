# HMNR MVEL Executor

Standalone Java 11 service for executing a restricted subset of MVEL expressions.

## Runtime

- Java 11
- Maven
- MVEL `2.5.2.Final`
- Jackson `2.17.2`

## Endpoints

- `GET /api/mvel/health`
- `POST /api/mvel/execute`

Example request:

```json
{
  "variables": {
    "firstName": "Ilham",
    "age": 24
  },
  "expression": "\"Hello \" + firstName"
}
```

The executor returns only primitive values, maps, and lists. Expressions and results are limited, execution is capped at 1000 ms, and known dangerous Java capabilities are rejected.

## Local run

```bash
mvn test
mvn package
java -jar target/mvel-executor-0.1.0.jar
```

## Docker

```bash
docker build -t hmnr-mvel-executor .
docker run --rm --network none --read-only --memory=256m --cpus=0.5 -p 8081:8081 hmnr-mvel-executor
```

This first executor slice is intentionally conservative. It does not expose arbitrary Java classes, filesystem access, network access, database access, application context, or environment variables to MVEL expressions. Production deployment should enforce the Docker limits above and add platform-level request authentication if the executor is not localhost-only.
