ALTER TABLE "recordsManagement"."archive" ADD COLUMN "originatorOwnerOrgRegNumber" text;
ALTER TABLE "recordsManagement"."archive" ADD COLUMN "originatingDate" date;

ALTER TABLE "recordsManagement"."log" ADD COLUMN "ownerOrgRegNumber" text;

ALTER TABLE "recordsManagement"."archivalProfile" ADD COLUMN "acceptArchiveWithoutProfile" boolean default true;

CREATE TABLE "recordsManagement"."archivalProfileContents"
(
	"parentProfileId" text NOT NULL,
	"containedProfileId" text NOT NULL,
	PRIMARY KEY ("parentProfileId", "containedProfileId"),
	FOREIGN KEY ("parentProfileId")
        REFERENCES "recordsManagement"."archivalProfile" ("archivalProfileId") MATCH SIMPLE
        ON UPDATE NO ACTION ON DELETE NO ACTION,
    FOREIGN KEY ("containedProfileId")
        REFERENCES "recordsManagement"."archivalProfile" ("archivalProfileId") MATCH SIMPLE
        ON UPDATE NO ACTION ON DELETE NO ACTION
);

CREATE TABLE "organization"."archivalProfileAccess"
(
  "orgId" text NOT NULL,
  "archivalProfileReference" text NOT NULL,
  "originatorAccess" boolean DEFAULT true,
  CONSTRAINT "archivalProfileAccess_orgId_fkey" FOREIGN KEY ("orgId")
      REFERENCES "organization"."organization" ("orgId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT "archivalProfileAccess_orgId_archivalProfileReference_key" UNIQUE ("orgId", "archivalProfileReference")
);

ALTER TABLE "recordsManagement"."archive" ALTER COLUMN "finalDisposition" DROP NOT NULL;
ALTER TABLE "recordsManagement"."archive" ALTER COLUMN "retentionDuration" DROP NOT NULL;
