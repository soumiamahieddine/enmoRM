-- Schema: contact

DROP SCHEMA IF EXISTS "documentManagement" CASCADE;

CREATE SCHEMA "documentManagement"
  AUTHORIZATION postgres;

-- Table: "documentManagement"."document"

-- DROP TABLE "documentManagement"."document";

CREATE TABLE "documentManagement"."document"
(
  "docId" text NOT NULL,
  "archiveId" text,
  "type" text NOT NULL,
  "control" text,
  "copy" boolean,
  
  "status" text,
  "description" text,
  "language" text,
  "purpose" text,
  "title" text,
  "creator" text,
  "publisher" text,
  "contributor" text,
  "category" text,
  "available" timestamp,
  "valid" timestamp,
  "creation" timestamp,
  "issue" timestamp,
  "receipt" timestamp,
  "response" timestamp,
  "submission" timestamp,
  "depositorDocId" text,
  "originatorDocId" text,
  PRIMARY KEY ("docId")
)
WITH (
  OIDS=FALSE
);


-- Table: "documentManagement"."documentRelationship"

-- DROP TABLE "documentManagement"."documentRelationship";

CREATE TABLE "documentManagement"."documentRelationship"
(
  "docId" text NOT NULL,
  "relatedDocId" text NOT NULL,
  "typeCode" text NOT NULL,
  "description" text,

  PRIMARY KEY ("docId", "relatedDocId", "typeCode"),
  FOREIGN KEY ("docId") 
    REFERENCES "documentManagement"."document" ("docId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY ("relatedDocId") 
    REFERENCES "documentManagement"."document" ("docId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
