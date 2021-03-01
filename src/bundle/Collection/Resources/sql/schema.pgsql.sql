-- Schema: Collection

DROP SCHEMA IF EXISTS "Collection" CASCADE;

CREATE SCHEMA "Collection";

-- Table: "Collection"."Collection"

-- DROP TABLE "Collection"."Collection";

CREATE TABLE "Collection"."Collection"
(
  "collectionId" text NOT NULL,
  "name" text,
  "archiveIds" jsonb
)
WITH (
  OIDS=FALSE
);

-- Table: "auth"."roleMember"

-- DROP TABLE IF EXISTS "Collection"."userCollection" CASCADE;

CREATE TABLE "Collection"."userCollection"
(
  "accountId" text,
  "collectionId" text NOT NULL,
  "orgId" text,
  FOREIGN KEY ("accountId")
      REFERENCES "auth"."account" ("accountId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY ("orgId")
      REFERENCES "organization"."organization" ("orgId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  UNIQUE ("accountId", "collectionId"),
  UNIQUE ("orgId", "collectionId")
)
WITH (
  OIDS=FALSE
);
