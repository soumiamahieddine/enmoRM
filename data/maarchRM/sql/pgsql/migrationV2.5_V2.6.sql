-- Migration script for PGSQL from Maarch RM V2.5 to V2.6

-- add columns for Digital Safe in auth.role
ALTER TABLE "auth"."role" ADD COLUMN "securityLevel" text;
