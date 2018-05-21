INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
  ('recordsManagement/outgoingTransfer', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Transfert sortant de l''archive %6$s');

DROP TABLE "organization"."orgRole";

ALTER TABLE "recordsManagement"."descriptionField" ADD COLUMN "isArray" boolean default false;