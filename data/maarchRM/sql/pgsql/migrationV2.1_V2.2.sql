INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
  ('recordsManagement/outgoingTransfer', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Transfert sortant de l''archive %6$s');

ALTER TABLE "recordsManagement"."retentionRule" ADD COLUMN "implementationDate" date;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "retentionRuleStatus" text;
INSERT INTO "auth"."servicePrivilege"("accountId", "serviceURI") VALUES ('System', 'recordsManagement/archives/updateArchivesretentionrule');
