-- Schema: contact

DROP SCHEMA IF EXISTS "recordsManagement" CASCADE;

CREATE SCHEMA "recordsManagement";

-- Table: "recordsManagement"."accessRule"

-- DROP TABLE "recordsManagement"."accessRule";

CREATE TABLE "recordsManagement"."accessRule"
(
  "code" text NOT NULL,
  "duration" text,
  "description" text NOT NULL,
  PRIMARY KEY ("code")
)
WITH (
  OIDS=FALSE
);


-- Table: "recordsManagement"."retentionRule"

-- DROP TABLE "recordsManagement"."retentionRule";

CREATE TABLE "recordsManagement"."retentionRule"
(
  "code" text NOT NULL,
  "duration" text NOT NULL,
  "finalDisposition" text,
  "description" text,
  "label" text NOT NULL,
  "implementationDate" date,

  PRIMARY KEY ("code")
)
WITH (
  OIDS=FALSE
);


-- Table: "recordsManagement"."archivalProfile"

-- DROP TABLE "recordsManagement"."archivalProfile";

CREATE TABLE "recordsManagement"."archivalProfile"
(
  "archivalProfileId" text NOT NULL,
  "reference" text NOT NULL,
  "name" text NOT NULL,
  "descriptionSchema" text,
  "descriptionClass" text,
  "retentionStartDate" text,
  "retentionRuleCode" text ,
  "description" text,
  "accessRuleCode" text,
  "acceptUserIndex" boolean default false,
  "acceptArchiveWithoutProfile" boolean default true,
  "fileplanLevel" text,
  "processingStatuses" jsonb,
  "isRetentionLastDeposit" boolean default false,
  "isDiscoverable" boolean default false,
  PRIMARY KEY ("archivalProfileId"),
  UNIQUE ("reference"),
  FOREIGN KEY ("accessRuleCode")
    REFERENCES "recordsManagement"."accessRule" ("code") MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY ("retentionRuleCode")
    REFERENCES "recordsManagement"."retentionRule" ("code") MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

CREATE TABLE "recordsManagement"."archivalProfileContents"
(
	"parentProfileId" text NOT NULL,
	"containedProfileId" text NOT NULL,
	PRIMARY KEY ("parentProfileId", "containedProfileId"),
	FOREIGN KEY ("parentProfileId")
    REFERENCES "recordsManagement"."archivalProfile" ("archivalProfileId") MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY ("containedProfileId")
    REFERENCES "recordsManagement"."archivalProfile" ("archivalProfileId") MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION
);


-- Table: "recordsManagement"."descriptionField"

-- DROP TABLE "recordsManagement"."descriptionField";

CREATE TABLE "recordsManagement"."descriptionField"
(
  "name" text NOT NULL,
  "label" text,
  "type" text,
  "default" text,
  "minLength" smallint,
  "maxLength" smallint,
  "minValue" numeric,
  "maxValue" numeric,
  "enumeration" text,
  "facets" jsonb,
  "pattern" text,
  "isArray" boolean default false,
  PRIMARY KEY ("name")
);

-- Table: "recordsManagement"."archiveDescription"

-- DROP TABLE "recordsManagement"."archiveDescription";

CREATE TABLE "recordsManagement"."archiveDescription"
(
  "archivalProfileId" text NOT NULL,
  "fieldName" text NOT NULL,
  "required" boolean,
  "position" integer,
  "isImmutable" boolean default false,
  "isRetained" boolean default true,
  "isInList" boolean default false,

  PRIMARY KEY ("archivalProfileId", "fieldName"),
  FOREIGN KEY ("archivalProfileId")
    REFERENCES "recordsManagement"."archivalProfile" ("archivalProfileId") MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

-- Table: "recordsManagement"."serviceLevel"

-- DROP TABLE "recordsManagement"."serviceLevel";

CREATE TABLE "recordsManagement"."serviceLevel"
(
  "serviceLevelId" text NOT NULL,
  "reference" text NOT NULL,
  "digitalResourceClusterId" text NOT NULL,
  "control" text,
  "default" boolean,
  "samplingFrequency" integer,
  "samplingRate" integer,
  PRIMARY KEY ("serviceLevelId"),
  UNIQUE ("reference")
)
 WITH (
  OIDS=FALSE
);


-- Table: "recordsManagement"."archive"

-- DROP TABLE "recordsManagement"."archive";

CREATE TABLE "recordsManagement"."archive"
(
  "archiveId" text NOT NULL,
  "originatorArchiveId" text,
  "depositorArchiveId" text,
  "archiverArchiveId" text,

  "archiveName" text,
  "storagePath" text,
  "filePlanPosition" text,
  "fileplanLevel" text,
  "originatingDate" date,

  "descriptionClass" text,
  "description" jsonb,
  "text" text,

  "originatorOrgRegNumber" text NOT NULL,
  "originatorOwnerOrgId" text,
  "originatorOwnerOrgRegNumber" text,
  "depositorOrgRegNumber" text,
  "archiverOrgRegNumber" text,
  "userOrgRegNumbers" text,

  "archivalProfileReference" text,
  "archivalAgreementReference" text,
  "serviceLevelReference" text,

  "retentionRuleCode" text,
  "retentionStartDate" date,
  "retentionDuration" text,
  "finalDisposition" text,
  "disposalDate" date,
  "retentionRuleStatus" text,

  "accessRuleCode" text,
  "accessRuleDuration" text,
  "accessRuleStartDate" date,
  "accessRuleComDate" date,

  "storageRuleCode" text,
  "storageRuleDuration" text,
  "storageRuleStartDate" date,
  "storageRuleEndDate" date,

  "classificationRuleCode" text,
  "classificationRuleDuration" text,
  "classificationRuleStartDate" date,
  "classificationEndDate" date,
  "classificationLevel" text,
  "classificationOwner" text,

  "depositDate" timestamp NOT NULL,
  "lastCheckDate" timestamp,
  "lastDeliveryDate" timestamp,
  "lastModificationDate" timestamp,

  "status" text NOT NULL,
  "processingStatus" text,

  "parentArchiveId" text,

  "fullTextIndexation" text default 'none',

  PRIMARY KEY ("archiveId"),
  FOREIGN KEY ("parentArchiveId")
    REFERENCES "recordsManagement"."archive" ("archiveId") MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY ("accessRuleCode")
    REFERENCES "recordsManagement"."accessRule" ("code") MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

CREATE INDEX
  ON "recordsManagement"."archive"
  ("filePlanPosition");

CREATE INDEX
  ON "recordsManagement"."archive"
  ("archivalProfileReference");

CREATE INDEX
  ON "recordsManagement"."archive"
  ("status");

CREATE INDEX
  ON "recordsManagement"."archive"
  ("originatorOrgRegNumber", "originatorArchiveId");

CREATE INDEX
  ON "recordsManagement"."archive"
  ("disposalDate");

CREATE INDEX
  ON "recordsManagement"."archive"
  USING gin (to_tsvector('french'::regconfig, translate("text", 'ÀÁÂÃÄÅàáâãäåÆæÞþČčĆćÇçĐđÈÉÊËèéêëÌÍÎÏìíîïÑñÒÓÔÕÖØðòóôõöøœŒŔŕŠšßÙÚÛÜùúûÝýÿŽž'::text, 'AAAAAAaaaaaaAEaeBbCcCcCcDjdjEEEEeeeeIIIIiiiiNnOOOOOOooooooooeOERrSsSsUUUUuuuYyyZz'::text)));



-- Table: "recordsManagement"."archiveRelationship"

-- DROP TABLE "recordsManagement"."archiveRelationship";

CREATE TABLE "recordsManagement"."archiveRelationship"
(
  "archiveId" text NOT NULL,
  "relatedArchiveId" text NOT NULL,
  "typeCode" text NOT NULL,
  "description" jsonb,

  PRIMARY KEY ("archiveId", "relatedArchiveId", "typeCode"),
  FOREIGN KEY ("archiveId")
	  REFERENCES "recordsManagement"."archive" ("archiveId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY ("relatedArchiveId")
	  REFERENCES "recordsManagement"."archive" ("archiveId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

-- Table: "recordsManagement"."log"

-- DROP TABLE "recordsManagement"."log";

CREATE TABLE "recordsManagement"."log"
(
  "archiveId" text NOT NULL,

  "fromDate" timestamp NOT NULL,
  "toDate" timestamp NOT NULL,
  "processId" text,
  "processName" text,
  "type" text NOT NULL,
  "ownerOrgRegNumber" text,

  PRIMARY KEY ("archiveId")
)
WITH (
  OIDS=FALSE
);

CREATE TABLE "recordsManagement"."descriptionClass"
(
  "name" text NOT NULL,
  "label" text NOT NULL,

PRIMARY KEY ("name")
)
WITH (
  OIDS=FALSE
);


-- Table: "recordsManagement"."storageRule"

-- DROP TABLE "recordsManagement"."storageRule";

CREATE TABLE "recordsManagement"."storageRule"
(
  "code" text NOT NULL,
  "duration" text NOT NULL,
  "description" text,
  "label" text,

  PRIMARY KEY ("code")
)
WITH (
  OIDS=FALSE
);

CREATE INDEX "recordsManagement_archive_originatingDate_idx" ON "recordsManagement"."archive" USING btree ("originatingDate");

CREATE INDEX "recordsManagement_archive_parentArchiveId_idx" ON "recordsManagement"."archive" USING btree ("parentArchiveId");

CREATE INDEX "recordsManagement_archive_descriptionClass_idx" ON "recordsManagement"."archive" USING btree ("descriptionClass");

CREATE INDEX "recordsManagement_archive_originatorOwnerOrgId_idx" ON "recordsManagement"."archive" USING btree ("originatorOwnerOrgId");

CREATE INDEX "recordsManagement_archive_archiverOrgRegNumber_idx" ON "recordsManagement"."archive" USING btree ("archiverOrgRegNumber");

CREATE INDEX "recordsManagement_archive_originatorOrgRegNumber_idx" ON "recordsManagement"."archive" USING btree ("originatorOrgRegNumber");

CREATE INDEX "recordsManagement_archive_originatorArchiveId_idx" ON "recordsManagement"."archive" USING btree ("originatorArchiveId");

CREATE INDEX "recordsManagement_archiveRelationship_archiveId_idx" ON "recordsManagement"."archiveRelationship" USING btree ("archiveId");

CREATE INDEX "recordsManagement_archiveRelationship_relatedArchiveId_idx" ON "recordsManagement"."archiveRelationship" USING btree ("relatedArchiveId");

CREATE SEQUENCE IF NOT EXISTS "recordsManagement"."archiverArchiveIdSequence";
