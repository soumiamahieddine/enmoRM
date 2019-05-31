-- Migration script for PGSQL from Maarch RM V2.4 to V2.5

-- Add columns for facets in descriptionField
ALTER TABLE "recordsManagement"."descriptionField" ADD COLUMN "facets" jsonb;

INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
 ('recordsManagement/depositNewResource', 'resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber format size', FALSE, 'Dépôt d''une ressource dans l''archive %6$s');
 