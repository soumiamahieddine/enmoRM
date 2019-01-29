ALTER TABLE "recordsManagement"."retentionRule" ALTER COLUMN "label" SET NOT NULL ;

INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
('recordsManagement/resourceDestruction', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Destruction de la ressource %9$s');