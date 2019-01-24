ALTER TABLE "recordsManagement"."retentionRule" ALTER COLUMN "label" SET NOT NULL ;

INSERT INTO "lifecycle"."eventFormat" (type, format, notification, message) VALUES 
('recordsManagement/destructionResource', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Destruction de la ressource %9$s');