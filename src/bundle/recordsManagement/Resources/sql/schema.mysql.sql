DROP TABLE IF EXISTS `recordsManagement.archiveRelationship`;
DROP TABLE IF EXISTS `recordsManagement.archive`;
DROP TABLE IF EXISTS `recordsManagement.archiveDescription`;
DROP TABLE IF EXISTS `recordsManagement.descriptionField`;
DROP TABLE IF EXISTS `recordsManagement.documentDescription`;
DROP TABLE IF EXISTS `recordsManagement.documentProfile`;
DROP TABLE IF EXISTS `recordsManagement.archivalProfile`;
DROP TABLE IF EXISTS `recordsManagement.accessRule`;
DROP TABLE IF EXISTS `recordsManagement.accessEntry`;
DROP TABLE IF EXISTS `recordsManagement.retentionRule`;
DROP TABLE IF EXISTS `recordsManagement.serviceLevel`;
DROP TABLE IF EXISTS `recordsManagement.log`;
DROP TABLE IF EXISTS `recordsManagement.descriptionClass`;

CREATE TABLE `recordsManagement.accessRule`
(
  `code` varchar(255) NOT NULL,
  `duration` text,
  `description` text NOT NULL,
  PRIMARY KEY (`code`)
);

CREATE TABLE `recordsManagement.accessEntry`
(
  `accessRuleCode` varchar(255) NOT NULL,
  `orgUnitId` varchar(255) NOT NULL,
  `readOnly` boolean DEFAULT true
);


CREATE TABLE `recordsManagement.retentionRule`
(
  `code` varchar(255) NOT NULL,
  `duration` text,
  `finalDisposition` text,
  `description` text,
  `label` text,
  PRIMARY KEY (`code`)
);


CREATE TABLE `recordsManagement.serviceLevel`
(
  `serviceLevelId` varchar(255) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `digitalResourceClusterId` text NOT NULL,
  `control` text,
  `default` boolean,
  PRIMARY KEY (`serviceLevelId`),
  UNIQUE (`reference`)
);


CREATE TABLE `recordsManagement.log`
(
  `archiveId` varchar(255) NOT NULL,
  
  `fromDate` timestamp(6) NOT NULL,
  `toDate` timestamp(6) NOT NULL,
  `processId` text,
  `processName` text,
  `type` text NOT NULL,

  PRIMARY KEY (`archiveId`)
);


CREATE TABLE `recordsManagement.descriptionClass`
(
  `name` varchar(255) NOT NULL,
  `label` text NOT NULL,

PRIMARY KEY (`name`)
);

CREATE TABLE `recordsManagement.archivalProfile`
(
  `archivalProfileId` varchar(255) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `name` text NOT NULL,
  `descriptionSchema` text,
  `descriptionClass` text,
  `retentionStartDate` text,
  `retentionRuleCode` varchar(255),
  `description` text,
  `accessRuleCode` varchar(255),
  `acceptUserIndex` boolean default false,
  `acceptMultipleDocuments` boolean default false,
  PRIMARY KEY (`archivalProfileId`),
  UNIQUE (`reference`),
  FOREIGN KEY (`accessRuleCode`)
    REFERENCES `recordsManagement.accessRule` (`code`) MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY (`retentionRuleCode`)
    REFERENCES `recordsManagement.retentionRule` (`code`) MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION
);


-- Table: `recordsManagement.descriptionField`

-- DROP TABLE `recordsManagement.descriptionField`;

CREATE TABLE `recordsManagement.descriptionField`
(
  `name` varchar(255) NOT NULL,
  `label` text,
  `type` text,
  `default` text,
  `minLength` smallint,
  `maxLength` smallint,
  `minValue` numeric,
  `maxValue` numeric,
  `enumeration` text,
  `pattern` text,
  PRIMARY KEY (`name`)
);

CREATE TABLE `recordsManagement.archiveDescription`
(
  `archivalProfileId` varchar(255) NOT NULL,
  `fieldName` varchar(255) NOT NULL,
  `origin` varchar(255),
  `required` boolean,
  PRIMARY KEY (`archivalProfileId`, `fieldName`),
  FOREIGN KEY (`archivalProfileId`)
    REFERENCES `recordsManagement.archivalProfile` (`archivalProfileId`) MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- Table: `recordsManagement.documentProfile`

-- DROP TABLE `recordsManagement.documentProfile`;

CREATE TABLE `recordsManagement.documentProfile`
(
  `archivalProfileId` varchar(255) NOT NULL,
  `documentProfileId` varchar(255) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `name` text NOT NULL,
  `required` boolean default true,
  `acceptUserIndex` boolean default false,
  PRIMARY KEY (`documentProfileId`),
  UNIQUE (`reference`),
  FOREIGN KEY (`archivalProfileId`)
    REFERENCES `recordsManagement.archivalProfile` (`archivalProfileId`) MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- Table: `recordsManagement.documentDescription`

-- DROP TABLE `recordsManagement.documentDescription`;

CREATE TABLE `recordsManagement.documentDescription`
(
  `documentProfileId` varchar(255) NOT NULL,
  `fieldName` varchar(255) NOT NULL,
  `origin` varchar(255),
  `required` boolean,
  `position` smallint,
  PRIMARY KEY (`documentProfileId`, `fieldName`),
  FOREIGN KEY (`documentProfileId`)
    REFERENCES `recordsManagement.documentProfile` (`documentProfileId`) MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION
);


CREATE TABLE `recordsManagement.archive`
(
  `archiveId` varchar(255) NOT NULL,
  `originatorArchiveId` text,
  `depositorArchiveId` text,
  
  `archiveName` text,
  `storagePath` text,

  `originatorOrgRegNumber` text NOT NULL,
  `originatorOwnerOrgId` text,
  `depositorOrgRegNumber` text,
  `archiverOrgRegNumber` text,

  `archivalProfileReference` text,
  `archivalAgreementReference` text,
  `serviceLevelReference` text,
  
  `retentionRuleCode` varchar(255),
  `retentionStartDate` date,
  `retentionDuration` text NOT NULL,
  `finalDisposition` text NOT NULL,
  `disposalDate` date,

  `accessRuleCode` varchar(255),
  `accessRuleDuration` text,
  `accessRuleStartDate` date,
  `accessRuleComDate` date,
  
  `depositDate` timestamp(6) NOT NULL,
  `lastCheckDate` timestamp(6),
  `lastDeliveryDate` timestamp(6),
  `lastModificationDate` timestamp(6),
  
  `status` text NOT NULL,

  `parentArchiveId` varchar(255),
  
  `descriptionClass` varchar(255),
  `descriptionId` varchar(255),

  PRIMARY KEY (`archiveId`),
  UNIQUE (`descriptionClass`, `descriptionId`),
  FOREIGN KEY (`parentArchiveId`)
    REFERENCES `recordsManagement.archive` (`archiveId`) MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY (`accessRuleCode`)
    REFERENCES `recordsManagement.accessRule` (`code`) MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY (`retentionRuleCode`)
    REFERENCES `recordsManagement.retentionRule` (`code`) MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION
);


CREATE TABLE `recordsManagement.archiveRelationship`
(
  `archiveId` varchar(255) NOT NULL,
  `relatedArchiveId` varchar(255) NOT NULL,
  `typeCode` varchar(255) NOT NULL,
  `description` text,

  PRIMARY KEY (`archiveId`, `relatedArchiveId`, `typeCode`),
  FOREIGN KEY (`archiveId`) 
	  REFERENCES `recordsManagement.archive` (`archiveId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY (`relatedArchiveId`) 
	  REFERENCES `recordsManagement.archive` (`archiveId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);