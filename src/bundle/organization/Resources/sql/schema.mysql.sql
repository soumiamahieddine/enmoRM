DROP TABLE IF EXISTS `organization.orgContact`;
DROP TABLE IF EXISTS `organization.userPosition`;
DROP TABLE IF EXISTS `organization.organization`;
DROP TABLE IF EXISTS `organization.orgRole`;
DROP TABLE IF EXISTS `organization.orgType`;
DROP TABLE IF EXISTS `organization.servicePosition`;


CREATE TABLE `organization.orgRole`
(
  `code` varchar(255) NOT NULL,
  `name` text,
  `description` text,
  PRIMARY KEY (`code`)
);


CREATE TABLE `organization.orgType`
(
  `code` varchar(255) NOT NULL,
  `name` text,
  PRIMARY KEY (`code`)
);


CREATE TABLE `organization.organization`
(
  `orgId` varchar(255) NOT NULL,
  `orgName` text NOT NULL,
  `otherOrgName` text,
  `displayName` text NOT NULL,

  `registrationNumber` varchar(255),
  `beginDate` date,
  `endDate` date,
  `legalClassification` text,
  `businessType` text,
  `description` text,
  `orgTypeCode` varchar(255),
  `orgRoleCodes` text,
  `taxIdentifier` varchar(255),
  `parentOrgId` varchar(255),
  `ownerOrgId` varchar(255),
  `history` text,

  `isOrgUnit` boolean,

  PRIMARY KEY (`orgId`),
  UNIQUE (`registrationNumber`),
  UNIQUE (`taxIdentifier`),
  FOREIGN KEY (`orgTypeCode`)
      REFERENCES `organization.orgType` (`code`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY (`parentOrgId`)
      REFERENCES `organization.organization` (`orgId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY (`ownerOrgId`)
      REFERENCES `organization.organization` (`orgId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);


CREATE TABLE `organization.userPosition`
(
  `userAccountId` varchar(255) NOT NULL,
  `orgId` varchar(255) NOT NULL,
  `function` text,
  `default` boolean,

  PRIMARY KEY (`userAccountId`, `orgId`),
  FOREIGN KEY (`orgId`)
      REFERENCES `organization.organization` (`orgId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);


CREATE TABLE `organization.servicePosition`
(
  `serviceAccountId` varchar(255) NOT NULL,
  `orgId` varchar(255) NOT NULL,

  PRIMARY KEY (`serviceAccountId`, `orgId`)
);


CREATE TABLE `organization.orgContact`
(
  `contactId` varchar(255) NOT NULL,
  `orgId` varchar(255) NOT NULL,
  `isSelf` boolean,

  PRIMARY KEY (`contactId`, `orgId`),
  FOREIGN KEY (`orgId`)
      REFERENCES `organization.organization` (`orgId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);
