
-- Table: "audit"."entry"*
DROP TABLE "entryRelationship";
DROP TABLE "entry";

CREATE TABLE "entry"
( 
    "entryId" VARCHAR2(512) NOT NULL,
    "entryDate" TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    "entryType" VARCHAR2(512) NOT NULL,
    "userId" VARCHAR2(512),
    "message" VARCHAR2(4000) NOT NULL,
	"variables" VARCHAR2(4000),
    "objectClass" VARCHAR2(512) NOT NULL,
    "objectId" VARCHAR2(512) NOT NULL,
    "dataContext" BLOB,
    PRIMARY KEY ("entryId")
);


-- Table: "entryRelationship"

CREATE TABLE "entryRelationship"
(
    "fromEntryId" VARCHAR2(512) NOT NULL,
    "toEntryId" VARCHAR2(512) NOT NULL,
    FOREIGN KEY ("fromEntryId")
        REFERENCES "entry" ("entryId"),
    FOREIGN KEY ("toEntryId")
        REFERENCES "entry" ("entryId")
);
