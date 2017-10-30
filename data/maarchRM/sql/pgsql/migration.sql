ALTER TABLE "recordsManagement"."archiveDescription" ADD COLUMN "position" integer;
ALTER TABLE "recordsManagement"."archiveDescription" DROP COLUMN "origin";
DROP TABLE "recordsManagement"."documentDescription";
DROP TABLE "recordsManagement"."documentProfile";

ALTER TABLE "recordsManagement"."archive" ADD COLUMN "archiverArchiveId" text;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "parentOriginatorOrgRegNumber" text;
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

