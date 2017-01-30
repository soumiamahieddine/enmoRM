-- Schema: organization

DROP SCHEMA IF EXISTS "filePlan" CASCADE;

CREATE SCHEMA "filePlan"
  AUTHORIZATION postgres;

-- Table: filePlan."subject"

-- DROP TABLE filePlan."subject";

CREATE TABLE "filePlan"."subject"
(
  "subjectId" text NOT NULL,
  "name" text NOT NULL UNIQUE,
  "parentSubjectId" text ,
  "description" text ,

  CONSTRAINT "subject_pkey" PRIMARY KEY ("subjectId"),
  CONSTRAINT "subject_filePlan_fkey" FOREIGN KEY ("parentSubjectId")
      REFERENCES "filePlan"."subject" ("subjectId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);


-- Table: filePlan."position"

-- DROP TABLE filePlan."position";

CREATE TABLE "filePlan"."position"
(
  "objectId" text NOT NULL,
  "objectClass" text NOT NULL,
  "label" text NOT NULL,
  "subjectId" text NOT NULL,
  
  CONSTRAINT position_Key UNIQUE ("objectId","subjectId"),
  CONSTRAINT "positionId_Pkey" PRIMARY KEY ("objectId","subjectId"),
  CONSTRAINT "position_filePlan_fkey" FOREIGN KEY ("objectId")
      REFERENCES "recordsManagement"."archive" ("archiveId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);