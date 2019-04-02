
DROP SCHEMA IF EXISTS "batchProcessing" CASCADE;

CREATE SCHEMA "batchProcessing";

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
  PRIMARY KEY ("schedulingId")
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


-- Table: "batchProcessing".notification

-- DROP TABLE "batchProcessing".notification;

CREATE TABLE "batchProcessing".notification
(
  "notificationId" text NOT NULL,
  "receivers" text NOT NULL,
  "message" text NOT NULL,
  "title" text NOT NULL,
  "createdDate" timestamp without time zone NOT NULL,
  "createdBy" text,
  "status" text NOT NULL,
  "sendDate" timestamp without time zone,
  "sendBy" text,
  PRIMARY KEY ("notificationId")
);