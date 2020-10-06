-- Migration script for PGSQL from Maarch RM V2.6 to V2.7

UPDATE "lifeCycle"."eventFormat" SET format = 'type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference code info' WHERE type = 'medona/sending';

ALTER TABLE "organization"."organization" ADD COLUMN "history" text;

