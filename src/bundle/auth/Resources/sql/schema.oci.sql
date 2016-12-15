DROP TABLE "privilege";
DROP TABLE "roleMember";
DROP TABLE "role";

CREATE TABLE "role"
( 
    "roleId"     VARCHAR2(512) NOT NULL,
    "roleName"   VARCHAR2(512) NOT NULL,
    "displayName" VARCHAR2(512) NOT NULL,
    "description" VARCHAR2(512),
    "enabled"     NUMBER(1) DEFAULT 1 NOT NULL,
    "picture"     BLOB,
    PRIMARY KEY ("roleId")
);

CREATE TABLE "roleMember"
( 
    "roleId" VARCHAR2(512) NOT NULL,
    "userId"  VARCHAR2(512) NOT NULL,
    FOREIGN KEY ("roleId")
        REFERENCES "role" ("roleId")
);

CREATE TABLE "privilege"
( 
    "roleId" VARCHAR2(512) NOT NULL,
    "route"   VARCHAR2(512) NOT NULL,
    FOREIGN KEY ("roleId")
        REFERENCES "role" ("roleId")
);