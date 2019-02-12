-- Migration script for PG SQL from Maarch RM V2.3 to V2.4

-- Set label as not null for retention rules
ALTER TABLE "recordsManagement"."retentionRule" ALTER COLUMN "label" SET NOT NULL ;


-- Add columns for processing statuses on archival profiles
ALTER TABLE "recordsManagement"."archivalProfile" ADD COLUMN "processingStatuses" jsonb;


-- Add columns for processing statuses on archival profiles
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "processingStatus" text;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "userOrgRegNumbers" text;


-- DROP constraint on orgid and add primary key constraint on couple (orgid and archivalprofilereference)
ALTER TABLE "organization"."archivalProfileAccess" DROP CONSTRAINT "archivalProfileAccess_orgId_archivalProfileReference_key";
ALTER TABLE "organization"."archivalProfileAccess" ADD CONSTRAINT  archivalProfileAccess_key PRIMARY KEY ("orgId", "archivalProfileReference");

-- Add columns for processing statuses on org unit archival profiles access
ALTER TABLE "organization"."archivalProfileAccess" ADD COLUMN "userAccess" jsonb;

-- Add columns for display or not in workflow list the archive descriptions
ALTER TABLE "recordsManagement"."archiveDescription" ADD COLUMN "isInList" boolean default false;
