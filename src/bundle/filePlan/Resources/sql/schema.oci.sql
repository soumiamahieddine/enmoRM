-- Schema: organization

--DROP SCHEMA IF EXISTS "filePlan" CASCADE;

--CREATE SCHEMA "filePlan"
  --AUTHORIZATION postgres;

-- Table: filePlan."filePlan"

-- DROP TABLE filePlan."filePlan";

CREATE TABLE "filePlan"
(
  "filePlanId" VARCHAR2(512) NOT NULL,
  "displayName" VARCHAR2(4000) NOT NULL,
  PRIMARY KEY ("filePlanId")
)
WITH (
  OIDS=FALSE
);


-- Table: filePlan."filePlanPositions"

-- DROP TABLE filePlan."filePlanPositions";

CREATE TABLE "filePlanPositions"
(
  "positionId" VARCHAR2(512) NOT NULL,
  "displayName" VARCHAR2(4000) NOT NULL,
  "parentId" VARCHAR2(512),
  "ownerFilePlanId" VARCHAR2(512) NOT NULL,
  PRIMARY KEY ("filePlanId", "positionId"),
  FOREIGN KEY ("ownerFilePlanId")
      REFERENCES "filePlan" ("filePlanId"),
  FOREIGN KEY ("parentId")
      REFERENCES "filePlanPositions" ("positionId")
)
WITH (
  OIDS=FALSE
);
