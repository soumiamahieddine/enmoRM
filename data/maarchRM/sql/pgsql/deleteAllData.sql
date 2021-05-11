-- Audit
TRUNCATE TABLE "audit"."event" CASCADE;

-- Auth
TRUNCATE TABLE "auth"."roleMember" CASCADE;
TRUNCATE TABLE "auth"."privilege" CASCADE;
TRUNCATE TABLE "auth"."servicePrivilege" CASCADE;
TRUNCATE TABLE "auth"."role" CASCADE;
TRUNCATE TABLE "auth"."account" CASCADE;

 -- BatchProcessing
TRUNCATE TABLE "batchProcessing"."scheduling" CASCADE;
TRUNCATE TABLE "batchProcessing"."logScheduling" CASCADE;
TRUNCATE TABLE "batchProcessing"."notification" CASCADE;

 -- Collection
TRUNCATE TABLE "collection"."collectionId" CASCADE;
TRUNCATE TABLE "collection"."name" CASCADE;
TRUNCATE TABLE "collection"."archiveIds" CASCADE;
TRUNCATE TABLE "collection"."accountId" CASCADE;
TRUNCATE TABLE "collection"."orgId" CASCADE;

 -- Contact
TRUNCATE TABLE "contact"."communication" CASCADE;
TRUNCATE TABLE "contact"."address" CASCADE;
TRUNCATE TABLE "contact"."contact" CASCADE;
TRUNCATE TABLE "contact"."communicationMean" CASCADE;

 -- DigitalResource
TRUNCATE TABLE "digitalResource"."digitalResource" CASCADE;
TRUNCATE TABLE "digitalResource"."address" CASCADE;
TRUNCATE TABLE "digitalResource"."clusterRepository" CASCADE;
TRUNCATE TABLE "digitalResource"."cluster" CASCADE;
TRUNCATE TABLE "digitalResource"."repository" CASCADE;
TRUNCATE TABLE "digitalResource"."conversionRule" CASCADE;
TRUNCATE TABLE "digitalResource"."contentType" CASCADE;
TRUNCATE TABLE "digitalResource"."package" CASCADE;
TRUNCATE TABLE "digitalResource"."packedResource" CASCADE;

 -- FilePlan
TRUNCATE TABLE "filePlan"."folder" CASCADE;
TRUNCATE TABLE "filePlan"."position" CASCADE;

 -- LifeCycle
TRUNCATE TABLE "lifeCycle"."eventFormat" CASCADE;
TRUNCATE TABLE "lifeCycle"."event" CASCADE;

 -- Organization
TRUNCATE TABLE "organization"."archivalProfileAccess" CASCADE;
TRUNCATE TABLE "organization"."userPosition" CASCADE;
TRUNCATE TABLE "organization"."servicePosition" CASCADE;
TRUNCATE TABLE "organization"."orgContact" CASCADE;
TRUNCATE TABLE "organization"."organization" CASCADE;
TRUNCATE TABLE "organization"."orgType" CASCADE;

 -- RecordsManagement
TRUNCATE TABLE "recordsManagement"."archiveRelationship" CASCADE;
TRUNCATE TABLE "recordsManagement"."archive" CASCADE;
TRUNCATE TABLE "recordsManagement"."archivalProfile" CASCADE;
TRUNCATE TABLE "recordsManagement"."serviceLevel" CASCADE;
TRUNCATE TABLE "recordsManagement"."accessRule" CASCADE;
TRUNCATE TABLE "recordsManagement"."retentionRule" CASCADE;
TRUNCATE TABLE "recordsManagement"."descriptionField" CASCADE;
TRUNCATE TABLE "recordsManagement"."archivalProfileContents" CASCADE;
TRUNCATE TABLE "recordsManagement"."archiveDescription" CASCADE;
TRUNCATE TABLE "recordsManagement"."descriptionClass" CASCADE;
TRUNCATE TABLE "recordsManagement"."log" CASCADE;
TRUNCATE TABLE "recordsManagement"."storageRule" CASCADE;

-- Medona
TRUNCATE TABLE "medona"."archivalAgreement" CASCADE;
TRUNCATE TABLE "medona"."message" CASCADE;
TRUNCATE TABLE "medona"."controlAuthority" CASCADE;
TRUNCATE TABLE "medona"."messageComment" CASCADE;
TRUNCATE TABLE "medona"."unitIdentifier" CASCADE;
