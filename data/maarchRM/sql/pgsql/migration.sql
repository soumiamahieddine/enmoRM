ALTER TABLE "recordsManagement"."archiveDescription" ADD COLUMN "position" integer;
ALTER TABLE "recordsManagement"."archiveDescription" DROP COLUMN "origin";
DROP TABLE "recordsManagement"."documentDescription";
DROP TABLE "recordsManagement"."documentProfile";
