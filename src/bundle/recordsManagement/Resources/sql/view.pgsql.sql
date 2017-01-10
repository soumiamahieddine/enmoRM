
DROP VIEW IF EXISTS "recordsManagement"."archiveDigitalResource";

CREATE VIEW "recordsManagement"."archiveDigitalResource" AS
SELECT 
"archive"."archiveId",
"archive"."originatorArchiveId",
"archive"."depositorArchiveId",
"archive"."archiveName",
"archive"."originatorOrgRegNumber",
"archive"."originatorOwnerOrgId",
"archive"."depositorOrgRegNumber",
"archive"."archiverOrgRegNumber",
"archive"."status" as "archiveStatus",
"archive"."archivalProfileReference",
"archive"."archivalAgreementReference",
"archive"."finalDisposition",
"archive"."disposalDate",
"archive"."descriptionClass",
"archive"."parentArchiveId",

"digitalResource"."resId",
"digitalResource"."clusterId",
"digitalResource"."size",
"digitalResource"."puid",
"digitalResource"."mimetype",
"digitalResource"."hash",
"digitalResource"."hashAlgorithm",
"digitalResource"."fileExtension",
"digitalResource"."fileName",
"digitalResource"."mediaInfo",
"digitalResource"."created",
"digitalResource"."updated",

"digitalResource"."relatedResId",
"digitalResource"."relationshipType"

FROM "recordsManagement"."archive"
   JOIN "digitalResource"."digitalResource" ON "digitalResource"."archiveId"="archive"."archiveId";