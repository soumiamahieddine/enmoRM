-- Migration script for PGSQL from Maarch RM V2.4 to V2.5

-- Add columns for enumerations names of enumerations in descriptionField
ALTER TABLE "recordsManagement"."descriptionField" ADD COLUMN "enumNames" text;
