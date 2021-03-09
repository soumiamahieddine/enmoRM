-- Schema: Collection

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
