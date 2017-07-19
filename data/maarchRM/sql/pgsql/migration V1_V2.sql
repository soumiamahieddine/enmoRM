ALTER TABLE "recordsManagement"."archivalProfile" ADD COLUMN "acceptArchiveWithoutProfile" boolean default true;

CREATE TABLE "recordsManagement"."archivalProfileRelationship"
(
	"parentProfileId" text NOT NULL,
	"relatedProfileId" text NOT NULL,
	PRIMARY KEY ("parentProfileId", "relatedProfileId"),
	FOREIGN KEY ("parentProfileId")
    REFERENCES "recordsManagement"."archivalProfile" ("archivalProfileId") MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION,
    FOREIGN KEY ("relatedProfileId")
    REFERENCES "recordsManagement"."archivalProfile" ("archivalProfileId") MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION
);
