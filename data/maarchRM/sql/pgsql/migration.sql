ALTER TABLE "medona"."archivalAgreement" ALTER COLUMN "beginDate" DROP NOT NULL;
ALTER TABLE "medona"."archivalAgreement" ALTER COLUMN "endDate" DROP NOT NULL;

UPDATE "lifeCycle"."eventFormat"
   SET format='resId hashAlgorithm hash address accessRuleStartDate accessRuleDuration previousAccessRuleStartDate previousAccessRuleDuration'
 WHERE type='recordsManagement/accessRuleModification';

UPDATE "lifeCycle"."eventFormat"
   SET format='resId hashAlgorithm hash address retentionStartDate retentionDuration finalDisposition previousStartDate previousDuration previousFinalDisposition'
 WHERE type='recordsManagement/retentionRuleModification';


CREATE TABLE IF NOT EXISTS "documentManagement"."documentRelationship"
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

DROP VIEW IF EXISTS "archivesPubliques"."archiveDescription";
CREATE OR REPLACE VIEW "archivesPubliques"."archiveDescription" AS
SELECT 
    "archive"."archiveId",
    "archive"."originatorArchiveId",
    "archive"."depositorArchiveId",
    "archive"."archiveName",
    "archive"."originatorOrgRegNumber",
    "archive"."originatorOwnerOrgId",
    "archive"."depositorOrgRegNumber",
    "archive"."archiverOrgRegNumber",
    "archive"."status",
    "archive"."archivalProfileReference",
    "archive"."archivalAgreementReference",
    "archive"."finalDisposition",
    "archive"."disposalDate",
    "archive"."descriptionClass",
    "archive"."descriptionId",
    "archive"."parentArchiveId",
	
    "contentDescription"."contentDescriptionId",
    "contentDescription"."description",
    "contentDescription"."descriptionLevel",
    "contentDescription"."filePlanPosition",
    "contentDescription"."language",
    "contentDescription"."latestDate",
    "contentDescription"."oldestDate",
    "contentDescription"."otherDescriptiveData",
    "contentDescription"."accessRuleCode",
    "contentDescription"."accessRuleDuration",
    "contentDescription"."accessRuleStartDate",
    "contentDescription"."accessRuleComDate",
    "contentDescription"."sedaXml",
    "contentDescription"."repositoryOrgRegNumber"
	
    FROM "recordsManagement"."archive" 
		LEFT JOIN "archivesPubliques"."contentDescription" ON "archive"."descriptionId" = "contentDescription"."contentDescriptionId"
    WHERE "archive"."descriptionClass" = 'archivesPubliques/contentDescription';


DROP VIEW IF EXISTS "archivesPubliques"."documentDescription";
CREATE OR REPLACE VIEW "archivesPubliques"."documentDescription" AS
SELECT 
	"archive"."archiveId",
	"archive"."originatorArchiveId",
	"archive"."depositorArchiveId",
	"archive"."archiveName",
	"archive"."originatorOrgRegNumber",
	"archive"."originatorOwnerOrgId",
	"archive"."depositorOrgRegNumber",
	"archive"."archiverOrgRegNumber",
	"archive"."status" as "archiveStatus",
	"archive"."archivalProfileReference",
	"archive"."archivalAgreementReference",
	"archive"."finalDisposition",
	"archive"."disposalDate",
	"archive"."descriptionClass",
	"archive"."descriptionId",
	"archive"."parentArchiveId",

	"contentDescription"."contentDescriptionId",
	"contentDescription"."description",
	"contentDescription"."descriptionLevel",
	"contentDescription"."filePlanPosition",
	"contentDescription"."language",
	"contentDescription"."latestDate",
	"contentDescription"."oldestDate",
	"contentDescription"."otherDescriptiveData",
	"contentDescription"."accessRuleCode",
	"contentDescription"."accessRuleDuration",
	"contentDescription"."accessRuleStartDate",
	"contentDescription"."accessRuleComDate",
	"contentDescription"."sedaXml",
	"contentDescription"."repositoryOrgRegNumber",
		
	"document"."docId",
	"document"."type",
	"document"."control",
	"document"."copy",
	"document"."status" as "documentStatus",
	"document"."depositorDocId",
	"document"."originatorDocId",

	"digitalResource"."resId",
	"digitalResource"."clusterId",
	"digitalResource"."size",
	"digitalResource"."puid",
	"digitalResource"."mimetype",
	"digitalResource"."hash",
	"digitalResource"."hashAlgorithm",
	"digitalResource"."fileExtension",
	"digitalResource"."fileName",
	"digitalResource"."mediaInfo",
	"digitalResource"."created",
	"digitalResource"."updated"
	
	FROM "recordsManagement"."archive"
		JOIN "documentManagement"."document" ON "document"."archiveId" = "archive"."archiveId"
		JOIN "digitalResource"."digitalResource" ON "document"."resId"="digitalResource"."resId"
		LEFT JOIN "archivesPubliques"."contentDescription" ON "archive"."descriptionId" = "contentDescription"."contentDescriptionId"
	WHERE "archive"."descriptionClass" = 'archivesPubliques/contentDescription';


DROP VIEW IF EXISTS "businessRecords"."administrativeRecord";
CREATE OR REPLACE VIEW "businessRecords"."administrativeRecord" AS 
 SELECT "adminDescription"."descriptionId",
    "adminDescription"."companyCode",
    "adminDescription"."companyName",
    "adminDescription"."thirdPartyCode",
    "adminDescription"."thirdPartyName",
    "adminDescription"."thirdPartyTaxIdentifier",
    "adminDescription"."reference",
    "adminDescription"."date",
    "adminDescription"."natureCode",
    "adminDescription"."year",
    "adminDescription"."quarter",
    "adminDescription"."month",
    "adminDescription"."relatedAgency",
    "adminDescription"."employeeNumber",
    "adminDescription"."employeeName",
    "archive"."archiveId",
    "archive"."originatorArchiveId",
    "archive"."depositorArchiveId",
    "archive"."originatorOrgRegNumber",
    "archive"."originatorOwnerOrgId",
    "archive"."depositorOrgRegNumber",
    "archive"."archiverOrgRegNumber",
    "archive"."status",
    "archive"."archivalProfileReference",
    "archive"."archivalAgreementReference",
    "archive"."finalDisposition",
    "archive"."disposalDate",
    "archive"."parentArchiveId",
    "archive"."archiveName"
  FROM "recordsManagement"."archive"
    LEFT JOIN "businessRecords"."adminDescription" ON "archive"."descriptionId" = "adminDescription"."descriptionId"
  WHERE "archive"."descriptionClass" = 'businessRecords/adminDescription';
  


CREATE TABLE IF NOT EXISTS "auth"."servicePrivilege"
(
  "accountId" text,
  "serviceURI" text,

  FOREIGN KEY ("accountId") 
    REFERENCES "auth"."account" ("accountId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

DO $$
  BEGIN
    BEGIN
      ALTER TABLE "lifeCycle"."eventFormat" ADD COLUMN "notification" boolean NOT NULL DEFAULT false;
    EXCEPTION
      WHEN duplicate_column THEN RAISE NOTICE 'column notification already exists in lifeCycle.eventFormat.';
    END;
    BEGIN
      ALTER TABLE "lifeCycle"."eventFormat" ADD COLUMN "message" text NOT NULL DEFAULT 'x';
    EXCEPTION
      WHEN duplicate_column THEN RAISE NOTICE 'column message already exists in lifeCycle.eventFormat.';
    END;
  END;
$$;

DO $$
  BEGIN
    BEGIN
      ALTER TABLE "lifeCycle"."event" ADD COLUMN "orgRegNumber" text NOT NULL DEFAULT '';
    EXCEPTION
        WHEN duplicate_column THEN RAISE NOTICE 'column orgRegNumber already exists in lifeCycle.event.';
    END;

    BEGIN
      ALTER TABLE "lifeCycle"."event" ADD COLUMN "orgUnitRegNumber" text NOT NULL DEFAULT '';
    EXCEPTION
        WHEN duplicate_column THEN RAISE NOTICE 'column orgUnitRegNumber already exists in lifeCycle.event.';
    END;

    BEGIN
      ALTER TABLE "lifeCycle"."event" ADD COLUMN "instanceName" text NOT NULL DEFAULT '';
    EXCEPTION
        WHEN duplicate_column THEN RAISE NOTICE 'column instanceName already exists in lifeCycle.event.';
    END;
  END;
$$;

-- V1.1
-- Document for Dublin Core metadata
ALTER TABLE "documentManagement"."document" ADD COLUMN "title" text;
ALTER TABLE "documentManagement"."document" ADD COLUMN "creator" text;
ALTER TABLE "documentManagement"."document" ADD COLUMN "publisher" text;
ALTER TABLE "documentManagement"."document" ADD COLUMN "contributor" text;
ALTER TABLE "documentManagement"."document" ADD COLUMN "category" text;
ALTER TABLE "documentManagement"."document" ADD COLUMN "available" timestamp;
ALTER TABLE "documentManagement"."document" ADD COLUMN "valid" timestamp;

DROP VIEW IF EXISTS "documentManagement"."archiveDocument";
CREATE VIEW "documentManagement"."archiveDocument" AS 
 SELECT "document"."docId",
    "document"."resId",
    "document"."archiveId",
    "document"."type",
    "document"."control",
    "document"."copy",
    "document"."description",
    "document"."language",
    "document"."purpose",
    "document"."creation",
    "document"."issue",
    "document"."receipt",
    "document"."response",
    "document"."submission",
	"document"."title",
	"document"."creator",
	"document"."publisher",
	"document"."contributor",
	"document"."category",
	"document"."available",
	"document"."valid",
    "document"."depositorDocId",
    "document"."originatorDocId",
	
	"archive"."originatorArchiveId",
    "archive"."depositorArchiveId",
    "archive"."originatorOrgRegNumber",
    "archive"."originatorOwnerOrgId",
    "archive"."depositorOrgRegNumber",
    "archive"."archiverOrgRegNumber",
    "archive"."status" as "archiveStatus",
    "archive"."archivalProfileReference",
    "archive"."archivalAgreementReference",
    "archive"."finalDisposition",
    "archive"."disposalDate",
    "archive"."parentArchiveId",
    "archive"."archiveName"
   FROM "recordsManagement"."archive"
     LEFT JOIN "documentManagement"."document" ON "archive"."archiveId" = "document"."archiveId";

-- Archival profile for lucene fulltext dans custom user fields
ALTER TABLE "recordsManagement"."archivalProfile" ADD COLUMN "acceptUserIndex" boolean default false;
ALTER TABLE "recordsManagement"."archivalProfile" ADD COLUMN "acceptMultipleDocuments" boolean default false;

-- Profile Description for lucene fields
ALTER TABLE "recordsManagement"."profileDescription" RENAME TO "archiveDescription";
ALTER TABLE "recordsManagement"."archiveDescription" RENAME COLUMN "propertyName" TO "fieldName";
ALTER TABLE "recordsManagement"."archiveDescription" ADD COLUMN "origin" text;

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
  "pattern" text,
  PRIMARY KEY ("name")
);

-- Table: "recordsManagement"."documentProfile"

-- DROP TABLE "recordsManagement"."documentProfile";

CREATE TABLE "recordsManagement"."documentProfile"
(
  "archivalProfileId" text NOT NULL,
  "documentProfileId" text NOT NULL,
  "reference" text NOT NULL,
  "name" text NOT NULL,
  "required" boolean default true,
  "acceptUserIndex" boolean default false,
  PRIMARY KEY ("documentProfileId"),
  UNIQUE ("reference")
)
WITH (
  OIDS=FALSE
);

-- Table: "recordsManagement"."documentDescription"

-- DROP TABLE "recordsManagement"."documentDescription";

CREATE TABLE "recordsManagement"."documentDescription"
(
  "documentProfileId" text NOT NULL,
  "fieldName" text NOT NULL,
  "origin" text,
  "required" boolean,
  PRIMARY KEY ("documentProfileId", "fieldName"),
  FOREIGN KEY ("documentProfileId")
    REFERENCES "recordsManagement"."documentProfile" ("documentProfileId") MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);


ALTER TABLE "audit"."event" ADD COLUMN "orgRegNumber" text;
ALTER TABLE "audit"."event" ADD COLUMN "orgUnitRegNumber" text;
ALTER TABLE "audit"."event" ADD COLUMN "instanceName" text;

ALTER TABLE "recordsManagement"."documentDescription" ADD COLUMN "position" integer;


ALTER TABLE "recordsManagement"."accessEntry" RENAME COLUMN "readOnly" TO "originatorAccess";
