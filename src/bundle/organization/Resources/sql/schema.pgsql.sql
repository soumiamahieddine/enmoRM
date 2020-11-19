-- Schema: organization

DROP SCHEMA IF EXISTS "organization" CASCADE;

CREATE SCHEMA "organization";

-- Table: "organization"."orgType"

-- DROP TABLE "organization"."orgType";

CREATE TABLE "organization"."orgType"
(
  "code" text NOT NULL,
  "name" text,
  PRIMARY KEY ("code")
)
WITH (
  OIDS=FALSE
);

-- Table: "organization"."organization"

-- DROP TABLE "organization"."organization";

CREATE TABLE "organization"."organization"
(
  "orgId" text NOT NULL,
  "orgName" text NOT NULL,
  "otherOrgName" text,
  "displayName" text NOT NULL,

  "registrationNumber" text NOT NULL,
  "beginDate" date,
  "endDate" date,
  "legalClassification" text,
  "businessType" text,
  "description" text,
  "orgTypeCode" text,
  "orgRoleCodes" text,
  "taxIdentifier" text,
  "parentOrgId" text,
  "ownerOrgId" text,
  "history" text,

  "isOrgUnit" boolean,
  "enabled" boolean,

  PRIMARY KEY ("orgId"),
  UNIQUE ("registrationNumber"),
  UNIQUE ("taxIdentifier"),
  FOREIGN KEY ("orgTypeCode")
      REFERENCES "organization"."orgType" ("code") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY ("parentOrgId")
      REFERENCES "organization"."organization" ("orgId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY ("ownerOrgId")
      REFERENCES "organization"."organization" ("orgId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);


-- Table: "organization"."userPosition"

-- DROP TABLE "organization"."userPosition";

CREATE TABLE "organization"."userPosition"
(
  "userAccountId" text NOT NULL,
  "orgId" text NOT NULL,
  "function" text,
  "default" boolean,

  PRIMARY KEY ("userAccountId", "orgId"),
  FOREIGN KEY ("orgId")
      REFERENCES "organization"."organization" ("orgId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);


-- Table: "organization"."servicePosition"

-- DROP TABLE "organization"."servicePosition";

CREATE TABLE "organization"."servicePosition"
(
  "serviceAccountId" text NOT NULL,
  "orgId" text NOT NULL,

  PRIMARY KEY ("serviceAccountId", "orgId")
)
WITH (
  OIDS=FALSE
);


-- Table: "organization"."orgContact"

-- DROP TABLE "organization"."orgContact";

CREATE TABLE "organization"."orgContact"
(
  "contactId" text NOT NULL,
  "orgId" text NOT NULL,
  "isSelf" boolean,

  PRIMARY KEY ("contactId", "orgId"),
  FOREIGN KEY ("orgId")
      REFERENCES "organization"."organization" ("orgId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);

-- Table: "organization"."archivalProfileAccess"

-- DROP TABLE "organization"."archivalProfileAccess";

CREATE TABLE "organization"."archivalProfileAccess"
(
  "orgId" text NOT NULL,
  "archivalProfileReference" text NOT NULL,
  "originatorAccess" boolean default true,
  "serviceLevelReference" text,
  "userAccess" jsonb,

  PRIMARY KEY ("orgId", "archivalProfileReference"),
  FOREIGN KEY ("orgId")
      REFERENCES "organization"."organization" ("orgId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);
