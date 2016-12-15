
DROP VIEW IF EXISTS "recordsManagement"."archiveDocumentDigitalResource";

CREATE VIEW "recordsManagement"."archiveDocumentDigitalResource" AS
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
"archive"."descriptionId",
"archive"."parentArchiveId",

"document"."docId",
"document"."type",
"document"."control",
"document"."copy",
"document"."status" as "documentStatus",
"document"."depositorDocId",
"document"."originatorDocId",

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
   JOIN "documentManagement"."document" ON "document"."archiveId"="archive"."archiveId"
   JOIN "digitalResource"."digitalResource" ON "document"."docId"="digitalResource"."docId";
