FROM maven:3.9.9-eclipse-temurin-11 AS build
WORKDIR /build
COPY pom.xml .
RUN mvn -q -DskipTests dependency:go-offline
COPY src ./src
RUN mvn -q package

FROM eclipse-temurin:11-jre
RUN useradd --system --uid 10001 mvel
WORKDIR /app
COPY --from=build /build/target/mvel-executor-0.1.0.jar /app/mvel-executor.jar
USER 10001
EXPOSE 8081
ENV MVEL_EXECUTOR_PORT=8081
ENTRYPOINT ["java", "-XX:MaxRAMPercentage=50", "-jar", "/app/mvel-executor.jar"]
