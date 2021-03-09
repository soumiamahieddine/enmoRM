-- Migration script for PGSQL from Maarch RM V2.7 to V2.8

ALTER TABLE "recordsManagement"."archivalProfile" ADD COLUMN "isRetentionLastDeposit" boolean default false;
