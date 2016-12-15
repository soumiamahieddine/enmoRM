-- Schema: contact


-- Table: "address"

DROP TABLE "address";
DROP TABLE "communication";
DROP TABLE "commMean";
DROP TABLE "contact";

CREATE TABLE "address"
(
  "addressId" VARCHAR2(512) NOT NULL,
  "purpose" VARCHAR2(4000),
  "occupancy" VARCHAR2(4000),
  "building" VARCHAR2(4000),
  "street" VARCHAR2(4000),
  "postBox" VARCHAR2(4000),
  "zipCode" VARCHAR2(4000),
  "placeName" VARCHAR2(4000),
  "stateName" VARCHAR2(4000),
  "nationName" VARCHAR2(4000),
  "objectId" VARCHAR2(512),
  "objectClass" VARCHAR2(512),
  PRIMARY KEY ("addressId")
);


-- Table: "commMean"


CREATE TABLE "commMean"
(
  "commMeanCode" VARCHAR2(4000) NOT NULL,
  "commMeanName" VARCHAR2(4000),
  "enabled" NUMBER(1),
  PRIMARY KEY ("commMeanCode")
);

-- Table: communication



CREATE TABLE "communication"
(
  "commId" VARCHAR2(512) NOT NULL,
  "purpose" VARCHAR2(4000),
  "commMeanCode" VARCHAR2(4000),
  "commValue" VARCHAR2(4000),
  "objectId" VARCHAR2(512),
  "objectClass" VARCHAR2(512),
  PRIMARY KEY ("commId"),
  FOREIGN KEY ("commMeanCode")
      REFERENCES "commMean" ("commMeanCode")
);



-- Table: contact



CREATE TABLE "contact"
(
  "contactId" VARCHAR2(512) NOT NULL,
  "partyType" VARCHAR2(512),
  "picture" BLOB,
  "orgName" VARCHAR2(4000),
  "otherOrgName" VARCHAR2(4000),
  "firstName" VARCHAR2(4000),
  "lastName" VARCHAR2(4000),
  "title" VARCHAR2(4000),
  "gender" VARCHAR2(4000),
  "birthName" VARCHAR2(4000),
  "dateOfBirth" date,
  "function" VARCHAR2(4000),
  "orgUnitName" VARCHAR2(4000),
  "displayName" VARCHAR2(4000),
  PRIMARY KEY ("contactId")
);