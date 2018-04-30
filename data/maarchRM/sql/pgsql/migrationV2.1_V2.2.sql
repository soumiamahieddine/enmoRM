INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
  ('recordsManagement/outgoingTransfer', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Transfert sortant de l''archive %6$s');



CREATE TABLE "recordsManagement"."storageRule"
(
  "code" text NOT NULL,
  "duration" text NOT NULL,
  "description" text,
  "label" text,

  PRIMARY KEY ("code")
)
WITH (
  OIDS=FALSE
);

ALTER TABLE "recordsManagement"."archive" ADD COLUMN "storageRuleCode" text;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "storageRuleDuration" text;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "storageRuleStartDate" date;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "storageRuleEndDate" date;