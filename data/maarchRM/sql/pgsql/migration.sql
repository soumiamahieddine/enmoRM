ALTER TABLE "recordsManagement"."archiveDescription" ADD COLUMN "position" integer;
ALTER TABLE "recordsManagement"."archiveDescription" DROP COLUMN "origin";
DROP TABLE "recordsManagement"."documentDescription";
DROP TABLE "recordsManagement"."documentProfile";

ALTER TABLE "recordsManagement"."archive" ADD COLUMN "archiverArchiveId" text;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "classificationRuleCode" text;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "classificationRuleDuration" text;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "classificationRuleStartDate" date;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "classificationEndDate" date;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "classificationLevel" text;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "classificationOwner" text;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "filePlanPosition" text;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "description" jsonb;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "text" text;


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
  USING gin
  (to_tsvector('french'::regconfig, "text"));

ALTER TABLE "recordsManagement"."accessEntry" RENAME COLUMN "orgUnitId" TO "orgRegNumber";

CREATE TABLE "digitalResource"."archiveDigitalResourceRel"
(
  "archiveId" text,
  "resId" text
)
WITH (
  OIDS=FALSE
);

INSERT INTO "digitalResource"."archiveDigitalResourceRel" SELECT "archiveId", "resId" FROM "digitalResource"."digitalResource";

CREATE OR REPLACE VIEW "digitalResource"."archiveDigitalResource" AS 
 SELECT "archiveDigitalResourceRel"."archiveId",
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
    "digitalResource"."updated",
    "digitalResource"."relatedResId",
    "digitalResource"."relationshipType"
   FROM "digitalResource"."archiveDigitalResourceRel"
     JOIN "digitalResource"."digitalResource" ON "digitalResource"."resId" = "archiveDigitalResourceRel"."resId";
	 
	 
ALTER TABLE "digitalResource"."digitalResource" DROP COLUMN "archiveId";

