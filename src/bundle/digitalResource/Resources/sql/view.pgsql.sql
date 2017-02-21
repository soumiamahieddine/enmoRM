DROP VIEW "digitalResource"."archiveDigitalResource";

CREATE OR REPLACE VIEW "digitalResource"."archiveDigitalResource" AS 
 SELECT "archiveDigitalResourceRel"."archiveId",
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
   FROM "digitalResource"."archiveDigitalResourceRel"
     JOIN "digitalResource"."digitalResource" ON "digitalResource"."resId" = "archiveDigitalResourceRel"."resId";
