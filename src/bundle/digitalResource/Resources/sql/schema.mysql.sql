DROP TABLE IF EXISTS `digitalResource.packedResource`;
DROP TABLE IF EXISTS `digitalResource.package`;
DROP TABLE IF EXISTS `digitalResource.clusterRepository`;
DROP TABLE IF EXISTS `digitalResource.address`;
DROP TABLE IF EXISTS `digitalResource.digitalResource`;
DROP TABLE IF EXISTS `digitalResource.cluster`;
DROP TABLE IF EXISTS `digitalResource.repository`;
DROP TABLE IF EXISTS `digitalResource.contentType`;
DROP TABLE IF EXISTS `digitalResource.conversionRule`;


CREATE TABLE `digitalResource.cluster`
(
  `clusterId` varchar(255) NOT NULL,
  `clusterName` text,
  `clusterDescription` text,
  PRIMARY KEY (`clusterId`)
);


CREATE TABLE `digitalResource.digitalResource`
(
  `resId` varchar(255) NOT NULL,
  `archiveId` varchar(255),
  `clusterId` varchar(255) NOT NULL,
  `size` integer NOT NULL,
  `puid` text,
  `mimetype` text,
  `hash` text,
  `hashAlgorithm` text,
  `fileExtension` text,
  `fileName` text,
  `mediaInfo` text,
  `created` timestamp(6) NOT NULL,
  `updated` timestamp(6),
  `relatedResId` varchar(255),
  `relationshipType` text,
  PRIMARY KEY (`resId`),
  FOREIGN KEY (`clusterId`)
      REFERENCES `digitalResource.cluster` (`clusterId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY (`relatedResId`)
      REFERENCES `digitalResource.digitalResource` (`resId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);


CREATE TABLE `digitalResource.repository`
(
  `repositoryId` varchar(255) NOT NULL,
  `repositoryName` text NOT NULL,
  `repositoryReference` varchar(255) NOT NULL,
  `repositoryType` text NOT NULL,
  `repositoryUri` varchar(255) NOT NULL,
  `parameters` text,
  `maxSize` integer,
  `enabled` boolean,
  PRIMARY KEY (`repositoryId`),
  UNIQUE (`repositoryReference`),
  UNIQUE (`repositoryUri`)
);


CREATE TABLE `digitalResource.address`
(
  `resId` varchar(255) NOT NULL,
  `repositoryId` varchar(255) NOT NULL,
  `path` text NOT NULL,
  `lastIntegrityCheck` timestamp(6),
  `integrityCheckResult` boolean,
  `packed` boolean default false,
  `created` timestamp(6) NOT NULL,
  PRIMARY KEY (`resId`, `repositoryId`),
  FOREIGN KEY (`resId`)
      REFERENCES `digitalResource.digitalResource` (`resId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY (`repositoryId`)
      REFERENCES `digitalResource.repository` (`repositoryId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);


CREATE TABLE `digitalResource.clusterRepository`
(
  `clusterId` varchar(255) NOT NULL,
  `repositoryId` varchar(255) NOT NULL,
  `writePriority` integer,
  `readPriority` integer,
  `deletePriority` integer,
  PRIMARY KEY (`clusterId`, `repositoryId`),
  FOREIGN KEY (`clusterId`)
      REFERENCES `digitalResource.cluster` (`clusterId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY (`repositoryId`)
      REFERENCES `digitalResource.repository` (`repositoryId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);


CREATE TABLE `digitalResource.package`
(
  `packageId` varchar(255) NOT NULL,
  `method` text NOT NULL,
  PRIMARY KEY (`packageId`),
  FOREIGN KEY (`packageId`)
      REFERENCES `digitalResource.digitalResource` (`resId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);



CREATE TABLE `digitalResource.packedResource`
(
  `packageId` varchar(255) NOT NULL,
  `resId` varchar(255) NOT NULL,
  `name` text NOT NULL,
  FOREIGN KEY (`packageId`)
      REFERENCES `digitalResource.package` (`packageId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY (`resId`)
      REFERENCES `digitalResource.digitalResource` (`resId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);


CREATE TABLE `digitalResource.contentType`
(
  `name` varchar(255) NOT NULL,
  `mediatype` text NOT NULL,
  `description` text,
  `puids` text,
  `validationMode` text,
  `conversionMode` text,
  `textExtractionMode` text,
  `metadataExtractionMode` text,
  PRIMARY KEY (`name`)
);


CREATE TABLE `digitalResource.conversionRule`
(
  `conversionRuleId` varchar(255) NOT NULL,
  `puid` varchar(255) NOT NULL,
  `conversionService` text NOT NULL,
  `targetPuid` text NOT NULL,
  PRIMARY KEY (`conversionRuleId`),
  UNIQUE (puid)
);
