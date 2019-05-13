-- Migration script for PGSQL from Maarch RM V2.4 to V2.5

-- Add columns for facets in descriptionField
ALTER TABLE "recordsManagement"."descriptionField" ADD COLUMN "facets" jsonb;
