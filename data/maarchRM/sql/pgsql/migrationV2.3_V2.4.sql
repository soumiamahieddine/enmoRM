-- Migration script for PG SQL from Maarch RM V2.3 to V2.4 

-- Set label as not null for retention rules
ALTER TABLE "recordsManagement"."retentionRule" ALTER COLUMN "label" SET NOT NULL ;


-- Add columns for processing statuses on archival profiles
ALTER TABLE "recordsManagement"."archivalProfile" ADD COLUMN "processingStatuses" jsonb;


-- Add columns for processing statuses on archival profiles
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "processingStatus" text;