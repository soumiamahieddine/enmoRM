-- Schema: audit

DROP SCHEMA IF EXISTS audit CASCADE;
CREATE SCHEMA "audit";

-- Table: audit.event

-- DROP TABLE audit.event;

CREATE TABLE "audit"."event"
(
  "eventId" text NOT NULL,
  "eventDate" timestamp NOT NULL,
  "accountId" text NOT NULL,
  "orgRegNumber" text,
  "orgUnitRegNumber" text,
  "path" text,
  "variables" json,
  "input" json,
  "output" text,
  "status" boolean default true,
  "info" text,
  "instanceName" text,
  PRIMARY KEY ("eventId")
)
WITH (
  OIDS=FALSE
);

CREATE INDEX "audit_event_instanceName_idx" ON "audit"."event" USING btree ("instanceName");

CREATE INDEX "audit_event_eventDate_idx" ON "audit"."event" USING btree ("eventDate");
