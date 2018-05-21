DROP SCHEMA IF EXISTS "auth" CASCADE;
CREATE SCHEMA "auth";

-- Table: "auth"."role"
DROP TABLE IF EXISTS "auth"."role" CASCADE;
CREATE TABLE "auth"."role"
(
  "roleId" text,
  "roleName" text NOT NULL,
  "description" text,
  "enabled" boolean DEFAULT true,
  
  PRIMARY KEY ("roleId")
)
WITH (
  OIDS=FALSE
);

-- Table: "auth"."account"
DROP TABLE IF EXISTS "auth"."account" CASCADE;
CREATE TABLE "auth"."account"
(
  "accountId" text NOT NULL,
  "accountName" text NOT NULL,
  "displayName" text NOT NULL,
  "accountType" text DEFAULT 'user',
  "emailAddress" text NOT NULL,
  "enabled" boolean DEFAULT true,
  
  "password" text,
  "passwordChangeRequired" boolean DEFAULT true,
  "passwordLastChange" timestamp,
  "locked" boolean DEFAULT false,
  "lockDate" timestamp,
  "badPasswordCount" integer,
  "lastLogin" timestamp,
  "lastIp" text,
  "replacingUserAccountId" text,
  "firstName" text,
  "lastName" text,
  "title" text,
  
  "salt" text,
  "tokenDate" timestamp,

  "authentication" jsonb,
  "preferencies" jsonb,
    
  PRIMARY KEY ("accountId"),
  UNIQUE ("accountName") 
)
WITH (
  OIDS=FALSE
);

-- Table: "auth"."roleMember"
DROP TABLE IF EXISTS "auth"."roleMember" CASCADE;
CREATE TABLE "auth"."roleMember"
(
  "roleId" text,
  "userAccountId" text NOT NULL,
  FOREIGN KEY ("roleId")
      REFERENCES "auth"."role" ("roleId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY ("userAccountId")
      REFERENCES "auth"."account" ("accountId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  UNIQUE ("roleId", "userAccountId")
)
WITH (
  OIDS=FALSE
);

-- Table: "auth".privileges
DROP TABLE IF EXISTS "auth"."privilege" CASCADE;
CREATE TABLE "auth"."privilege"
(
  "roleId" text,
  "userStory" text,
  FOREIGN KEY ("roleId")
      REFERENCES "auth"."role" ("roleId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  UNIQUE ("roleId", "userStory")
)
WITH (
  OIDS=FALSE
);

-- Table: "auth"."servicePrivilege"
DROP TABLE IF EXISTS "auth"."servicePrivilege" CASCADE;
CREATE TABLE "auth"."servicePrivilege"
(
  "accountId" text,
  "serviceURI" text,
  FOREIGN KEY ("accountId")
      REFERENCES auth.account ("accountId") MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  UNIQUE ("accountId", "serviceURI")
)
WITH (
  OIDS=FALSE
);