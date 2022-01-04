-- Migration script for PGSQL from Maarch RM V2.7 to V2.8

DROP SCHEMA IF EXISTS "Collection" CASCADE;

CREATE SCHEMA "Collection";

-- Table: "Collection"."Collection"

CREATE TABLE "Collection"."Collection"
(
  "collectionId" text NOT NULL,
  "name" text,
  "archiveIds" jsonb,
  "accountId" text,
  "orgId" text,
  FOREIGN KEY ("accountId")
      REFERENCES "auth"."account" ("accountId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY ("orgId")
      REFERENCES "organization"."organization" ("orgId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  PRIMARY KEY ("accountId")
)
WITH (
  OIDS=FALSE
);

ALTER TABLE "recordsManagement"."archivalProfile" ADD COLUMN "isRetentionLastDeposit" boolean default false;
ALTER TABLE "recordsManagement"."archivalProfile" ADD COLUMN "isDiscoverable" boolean default false;

INSERT INTO "lifeCycle"."eventFormat" ("type","format","message","notification") VALUES
('medona/authorization', 'type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference', 'Traitement du message %14$s de type %9$s de %11$s (%10$s) par %13$s (%12$s)', false);
