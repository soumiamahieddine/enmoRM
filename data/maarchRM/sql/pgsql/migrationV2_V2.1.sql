ALTER TABLE "organization"."archivalProfileAccess" ADD COLUMN "serviceLevelReference" text;

DROP TABLE "batchProcessing"."task";
DROP TABLE "auth"."publicUserStory";