ALTER TABLE "recordsManagement"."archivalProfile" ADD COLUMN "acceptArchiveWithoutProfile" boolean default true;

CREATE TABLE "recordsManagement"."archivalProfileRelationship"
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