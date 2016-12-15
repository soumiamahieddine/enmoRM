-- Schema: organization
-- DROP USER "organization" CASCADE;

-- Table: "person"

DROP TABLE "position";
DROP TABLE "person";
DROP TABLE "orgUnit";
DROP TABLE "organization";
DROP TABLE "orgRole";
DROP TABLE "orgType";
DROP TABLE "orgUnitType";

CREATE TABLE "person"
(
  "personId"   VARCHAR2(512)  NOT NULL,
  "firstName"     VARCHAR2(4000),
  "lastName"      VARCHAR2(4000),
  "title"         VARCHAR2(4000),
  "displayName"   VARCHAR2(4000)  NOT NULL,

  "birthName"     VARCHAR2(4000),
  "gender"        VARCHAR2(4000),
  "dateOfBirth"   DATE,
  "picture"       BLOB,
  
  PRIMARY KEY ("personId")
);

-- Table: "orgRole"



CREATE TABLE "orgRole"
(
  "orgRoleCode" VARCHAR2(4000) NOT NULL,
  "orgRoleName" VARCHAR2(4000),
  PRIMARY KEY ("orgRoleCode")
);

-- Table: "orgType"



CREATE TABLE "orgType"
(
  "orgTypeCode" VARCHAR2(4000) NOT NULL,
  "orgTypeName" VARCHAR2(4000),
  PRIMARY KEY ("orgTypeCode")
);

-- Table: organization



CREATE TABLE "organization"
(
  "orgId"               VARCHAR2(512) NOT NULL,
  "orgName"             VARCHAR2(4000) NOT NULL,
  "displayName"         VARCHAR2(4000) NOT NULL,

  "orgTypeCode"         VARCHAR2(4000),
  "orgRoleCodes"        VARCHAR2(4000),
  "legalClassification" VARCHAR2(4000),
  "taxIdentifier"       VARCHAR2(4000),
  "registrationNumber"  VARCHAR2(4000),
  "otherOrgName"        VARCHAR2(4000),
  "picture"             BLOB,
  PRIMARY KEY ("orgId"),
  FOREIGN KEY ("orgTypeCode")
      REFERENCES "orgType" ("orgTypeCode")
);


-- Table: "orgUnitType"

-- 

CREATE TABLE "orgUnitType"
(
  "orgUnitTypeCode" VARCHAR2(4000) NOT NULL,
  "orgUnitTypeName" VARCHAR2(4000),
  PRIMARY KEY ("orgUnitTypeCode")
);


-- Table: "orgUnit"

-- DROP TABLE "orgUnit";

CREATE TABLE "orgUnit"
(
  "orgUnitId"       VARCHAR2(512) NOT NULL,
  "orgUnitName"     VARCHAR2(4000),
  "displayName"     VARCHAR2(4000) NOT NULL,

  "orgUnitTypeCode" VARCHAR2(4000),
  "ownerOrgId"      VARCHAR2(512),
  "parentOrgUnitId" VARCHAR2(512),
  "picture"         BLOB,

  PRIMARY KEY ("orgUnitId"),
  FOREIGN KEY ("ownerOrgId")
      REFERENCES "organization" ("orgId"),
  FOREIGN KEY ("parentOrgUnitId")
      REFERENCES "orgUnit" ("orgUnitId")
);

-- Table: "position"

-- DROP TABLE "position";

CREATE TABLE "position"
(
  "positionId" VARCHAR2(512) NOT NULL,
  "objectId"    VARCHAR2(512) NOT NULL,
  "objectClass"  VARCHAR2(512) NOT NULL,
  "orgUnitId"   VARCHAR2(512) NOT NULL,
  "function"    VARCHAR2(4000) NOT NULL,
  PRIMARY KEY ("positionId"),
  UNIQUE ("objectId", "objectClass", "orgUnitId"),
  FOREIGN KEY ("orgUnitId")
      REFERENCES "orgUnit" ("orgUnitId")
);
