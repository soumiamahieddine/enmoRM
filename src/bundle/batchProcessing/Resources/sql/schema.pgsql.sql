
DROP SCHEMA IF EXISTS "batchProcessing" CASCADE;

CREATE SCHEMA "batchProcessing"
  AUTHORIZATION postgres;

-- Table: "batchProcessing"."task"

-- DROP TABLE "batchProcessing"."task";

CREATE TABLE "batchProcessing"."task"
(
  "taskId" text NOT NULL,
  "route" text,
  "description" text,
  PRIMARY KEY ("taskId")
);

-- Table: "batchProcessing"."scheduling"

-- DROP TABLE "batchProcessing"."scheduling";

CREATE TABLE "batchProcessing"."scheduling"
(
  "schedulingId" text NOT NULL,
  "name" text NOT NULL,
  "taskId" text NOT NULL,
  "frequency" text NOT NULL,
  "parameters" text,
  "executedBy" text NOT NULL,
  "lastExecution" timestamp,
  "nextExecution" timestamp,
  "status" text,
  PRIMARY KEY ("schedulingId"),
  FOREIGN KEY ("taskId")
    REFERENCES "batchProcessing"."task" ("taskId") MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);


-- Table: "batchProcessing"."logScheduling"

-- DROP TABLE "batchProcessing"."logScheduling";

CREATE TABLE "batchProcessing"."logScheduling"
(
  "logId" text NOT NULL,
  "schedulingId" text NOT NULL,
  "executedBy" text NOT NULL,
  "launchedBy" text NOT NULL,
  "logDate" timestamp NOT NULL,
  "status" boolean default true,
  "info" text,
  PRIMARY KEY ("logId")
);
