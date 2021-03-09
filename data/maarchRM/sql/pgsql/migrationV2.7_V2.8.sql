-- Migration script for PGSQL from Maarch RM V2.7 to V2.8

<<<<<<< HEAD
DROP SCHEMA IF EXISTS "Collection" CASCADE;

CREATE SCHEMA "Collection";

-- Table: "Collection"."Collection"

-- DROP TABLE "Collection"."Collection";

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

=======
ALTER TABLE "recordsManagement"."archivalProfile" ADD COLUMN "isRetentionLastDeposit" boolean default false;
>>>>>>> feat/#16379 : Elimination date based on the last deposit
