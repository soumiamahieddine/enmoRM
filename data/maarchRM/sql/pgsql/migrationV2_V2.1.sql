ALTER TABLE "organization"."archivalProfileAccess" ADD COLUMN "serviceLevelReference" text;

ALTER TABLE "digitalResource"."digitalResource" ALTER COLUMN "size" TYPE bigint;