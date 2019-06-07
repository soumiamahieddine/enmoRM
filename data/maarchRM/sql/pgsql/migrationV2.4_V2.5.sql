INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
 ('recordsManagement/depositNewResource', 'resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber format size', FALSE, 'Dépôt d''une ressource dans l''archive %6$s');

 ALTER TABLE "medona"."message" ADD COLUMN "comment" text;
 DROP TABLE "medona"."messageComment";