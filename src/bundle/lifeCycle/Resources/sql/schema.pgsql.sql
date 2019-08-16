-- Schema: lifeCycle

DROP SCHEMA IF EXISTS "lifeCycle" CASCADE;

CREATE SCHEMA "lifeCycle";


-- Table: "lifeCycle"."event"

-- DROP TABLE "lifeCycle"."event";

CREATE TABLE "lifeCycle"."event"
(
  "eventId" text NOT NULL,
  "eventType" text NOT NULL,
  "timestamp" timestamp NOT NULL,
  "instanceName" text NOT NULL,
  "orgRegNumber" text,
  "orgUnitRegNumber" text,
  "accountId" text,
  "objectClass" text NOT NULL,
  "objectId" text NOT NULL,
  "operationResult" boolean,
  "description" text,
  "eventInfo" text,
  PRIMARY KEY ("eventId")
)
WITH (
  OIDS=FALSE
);


-- Table: "lifeCycle"."eventFormat"

-- DROP TABLE "lifeCycle"."eventFormat";

CREATE TABLE "lifeCycle"."eventFormat"
(
  "type" text NOT NULL,
  "format" text NOT NULL,
  "message" text NOT NULL,
  "notification" boolean DEFAULT false,
  PRIMARY KEY ("type")
)
WITH (
  OIDS=FALSE
);


CREATE INDEX
  ON "lifeCycle"."event"
  ("objectId");

CREATE INDEX
  ON "lifeCycle"."event"
  ("timestamp");

CREATE INDEX "lifeCycle_event_objectClass_idx" ON "lifeCycle"."event" USING btree ("objectClass");

CREATE INDEX "lifeCycle_event_instanceName_idx" ON "lifeCycle"."event" USING btree ("instanceName");

CREATE INDEX "lifeCycle_event_eventType_idx" ON "lifeCycle"."event" USING btree ("eventType");

CREATE INDEX "lifeCycle_event_objectClass_objectId_idx" ON "lifeCycle"."event" USING btree ("objectClass", "objectId");
