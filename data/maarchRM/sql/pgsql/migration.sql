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
