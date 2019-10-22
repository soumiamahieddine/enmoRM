-- Schema: organization

DROP SCHEMA IF EXISTS "filePlan" CASCADE;

CREATE SCHEMA "filePlan";

-- Table: filePlan."subject"

-- DROP TABLE filePlan."subject";

CREATE TABLE "filePlan"."folder"
(
  "folderId" text NOT NULL,
  "name" text NOT NULL,
  "parentFolderId" text,
  "description" text,
  "ownerOrgRegNumber" text,
  "closed" boolean,

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

CREATE INDEX "filePlan_folder_ownerOrgRegNumber_idx" ON "filePlan"."folder" USING btree ("ownerOrgRegNumber");
