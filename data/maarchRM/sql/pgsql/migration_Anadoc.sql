INSERT INTO "organization"."orgRole" ("code", "name","description") VALUES
('classifiedArchivesAccess','anadoc/classified', 'Classified archives access');

ALTER TABLE "recordsManagement"."archiveRelationship" DROP COLUMN "description";
ALTER TABLE "recordsManagement"."archiveRelationship" ADD COLUMN "description" jsonb;