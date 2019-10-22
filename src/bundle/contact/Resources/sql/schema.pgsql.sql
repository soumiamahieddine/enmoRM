-- Schema: contact

DROP SCHEMA IF EXISTS "contact" CASCADE;

CREATE SCHEMA "contact";

-- Table: "contact"."contact"

-- DROP TABLE "contact"."contact";

CREATE TABLE "contact"."contact"
(
  "contactId" text NOT NULL,
  "contactType" text NOT NULL DEFAULT 'person',
  "orgName" text,
  "firstName" text,
  "lastName" text,
  "title" text,
  "function" text,
  "service" text,
  "displayName" text,
  PRIMARY KEY ("contactId")
)
WITH (
  OIDS=FALSE
);

-- Table: "contact".address

-- DROP TABLE "contact".address;

CREATE TABLE "contact"."address"
(
  "addressId" text NOT NULL,
  "contactId" text NOT NULL,
  "purpose" text NOT NULL,
  "room" text,
  "floor" text,
  "building" text,
  "number" text,
  "street" text,
  "postBox" text,
  "block" text,
  "citySubDivision" text,
  "postCode" text,
  "city" text,
  "country" text,
  UNIQUE ("contactId", "purpose"),
  PRIMARY KEY ("addressId"),
  FOREIGN KEY ("contactId") 
    REFERENCES "contact"."contact" ("contactId") MATCH SIMPLE
	ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);


-- Table: "contact"."communicationMean"

-- DROP TABLE "contact"."communicationMean";

CREATE TABLE "contact"."communicationMean"
(
  "code" text NOT NULL,
  "name" text NOT NULL,
  "enabled" boolean,
  PRIMARY KEY ("code"),
  UNIQUE ("name")
)
WITH (
  OIDS=FALSE
);

-- Table: "contact"."communication"

-- DROP TABLE "contact"."communication";

CREATE TABLE "contact"."communication"
(
  "communicationId" text NOT NULL,
  "contactId" text NOT NULL,
  "purpose" text NOT NULL,
  "comMeanCode" text NOT NULL,
  "value" text NOT NULL,
  "info" text,
  PRIMARY KEY ("communicationId"),
  UNIQUE ("contactId", "purpose", "comMeanCode"),
  FOREIGN KEY ("contactId") 
    REFERENCES "contact"."contact" ("contactId") MATCH SIMPLE
	ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY ("comMeanCode")
      REFERENCES "contact"."communicationMean" ("code") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (
  OIDS=FALSE
);



