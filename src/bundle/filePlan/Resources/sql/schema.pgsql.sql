-- Schema: organization

DROP SCHEMA IF EXISTS "filePlan" CASCADE;

CREATE SCHEMA "filePlan"
  AUTHORIZATION postgres;

-- Table: filePlan."subject"

-- DROP TABLE filePlan."subject";

CREATE TABLE "filePlan"."folder"
(
  "folderId" text NOT NULL,
  "name" text NOT NULL UNIQUE,
  "parentFolderId" text,
  "description" text,
  "ownerOrgRegNumber" text,
  "disabled" boolean,

  CONSTRAINT "folder_pkey" PRIMARY KEY ("folderId"),
  CONSTRAINT "filePlan_name_parentFolderId_key" UNIQUE ("name", "parentFolderId"),
  CONSTRAINT "folderId_filePlan_fkey" FOREIGN KEY ("parentFolderId")
      REFERENCES "filePlan"."folder" ("folderId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);


-- Table: filePlan."position"

-- DROP TABLE filePlan."position";

CREATE TABLE "filePlan"."position"
(
  "folderId" text NOT NULL,
  "archiveId" text NOT NULL,
  
  CONSTRAINT "position_filePlan_fkey" FOREIGN KEY ("folderId")
      REFERENCES "filePlan"."folder" ("folderId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);


-- DROP VIEW "filePlan"."archivePosition";

CREATE OR REPLACE VIEW "filePlan"."archivePosition" AS 
 SELECT "archive"."archiveId",
    "archive"."archiveName",
	"archive"."originatorArchiveId",
    "archive"."archivalProfileReference",
    "archive"."depositDate",
    "archive"."originatorOrgRegNumber" AS "ownerOrgRegNumber",
    "archive"."filePlanPosition" AS "folderId"
   FROM "recordsManagement"."archive"

UNION   
 SELECT "archive"."archiveId",
    "archive"."archiveName",
	"archive"."originatorArchiveId",
    "archive"."archivalProfileReference",
    "archive"."depositDate",
    "folder"."ownerOrgRegNumber",
    "folder"."folderId"
   FROM "recordsManagement"."archive"
     JOIN "filePlan"."position" ON "position"."archiveId" = "archive"."archiveId"
     JOIN "filePlan"."folder" ON "folder"."folderId" = "position"."folderId"

UNION
 SELECT "archive"."archiveId",
    "archive"."archiveName",
	"archive"."originatorArchiveId",
    "archive"."archivalProfileReference",
    "archive"."depositDate",
    "accessEntry"."orgRegNumber" AS "ownerOrgRegNumber",
    "folder"."folderId"
   FROM "recordsManagement"."archive"
     JOIN "recordsManagement"."accessEntry" ON "archive"."accessRuleCode" = "accessEntry"."accessRuleCode"
     LEFT JOIN "filePlan"."position" ON "position"."archiveId" = "archive"."archiveId"
     LEFT JOIN "filePlan"."folder" ON "folder"."folderId" = "position"."folderId" AND "folder"."ownerOrgRegNumber" = "accessEntry"."orgRegNumber" AND "archive"."originatorOrgRegNumber" <> "folder"."ownerOrgRegNumber"
  WHERE "position"."folderId" IS NULL AND "accessEntry"."orgRegNumber" <> "archive"."originatorOrgRegNumber";
