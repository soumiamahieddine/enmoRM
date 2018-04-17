-- Audit
DELETE FROM "audit"."event";

-- Auth
DELETE FROM "auth"."roleMember";
DELETE FROM "auth"."privilege";
DELETE FROM "auth"."servicePrivilege";
DELETE FROM "auth"."role";
DELETE FROM "auth"."account";

 -- BatchProcessing
DELETE FROM "batchProcessing"."scheduling";
DELETE FROM "batchProcessing"."task";

 -- Contact
DELETE FROM "contact"."communication";
DELETE FROM "contact"."address";
DELETE FROM "contact"."contact";
DELETE FROM "contact"."communicationMean";

 -- DigitalResource
TRUNCATE "digitalResource"."digitalResource" CASCADE;
TRUNCATE "digitalResource"."address" CASCADE;
TRUNCATE "digitalResource"."clusterRepository" CASCADE;
TRUNCATE "digitalResource"."cluster" CASCADE;
TRUNCATE "digitalResource"."repository" CASCADE;

 -- FilePlan
DELETE FROM "filePlan"."folder";
DELETE FROM "filePlan"."position";

 -- LifeCycle
TRUNCATE TABLE "lifeCycle"."eventFormat" CASCADE;

 -- Organization
DELETE FROM "organization"."archivalProfileAccess";
DELETE FROM "organization"."userPosition";
DELETE FROM "organization"."servicePosition";
DELETE FROM "organization"."orgContact";
DELETE FROM "organization"."organization";
DELETE FROM "organization"."orgType";

 -- RecordsManagement
TRUNCATE TABLE "recordsManagement"."archiveRelationship" CASCADE;
TRUNCATE TABLE "recordsManagement"."archive" CASCADE;

TRUNCATE TABLE "recordsManagement"."archivalProfile" CASCADE;
TRUNCATE TABLE "recordsManagement"."serviceLevel" CASCADE;

TRUNCATE TABLE "recordsManagement"."accessRule" CASCADE;
TRUNCATE TABLE "recordsManagement"."retentionRule" CASCADE;

TRUNCATE TABLE "recordsManagement"."descriptionField" CASCADE;