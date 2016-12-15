DROP TABLE IF EXISTS `documentManagement.documentRelationship`;
DROP TABLE IF EXISTS `documentManagement.document`;

CREATE TABLE `documentManagement.document`
(
  `docId` varchar(255) NOT NULL,
  `archiveId` text,
  `type` text NOT NULL,
  `control` text,
  `copy` boolean,
  
  `status` text,
  `description` text,
  `language` text,
  `purpose` text,
  `title` text,
  `creator` text,
  `publisher` text,
  `contributor` text,
  `category` text,
  `available` timestamp(6),
  `valid` timestamp(6),
  
  `creation` timestamp(6),
  `issue` timestamp(6),
  `receipt` timestamp(6),
  `response` timestamp(6),
  `submission` timestamp(6),
  `depositorDocId` text,
  `originatorDocId` text,
  PRIMARY KEY (`docId`)
);


CREATE TABLE `documentManagement.documentRelationship`
(
  `docId` varchar(255) NOT NULL,
  `relatedDocId` varchar(255) NOT NULL,
  `typeCode` varchar(255) NOT NULL,
  `description` text,

  PRIMARY KEY (`docId`, `relatedDocId`, `typeCode`),
  FOREIGN KEY (`docId`) 
    REFERENCES `documentManagement.document` (`docId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY (`relatedDocId`) 
    REFERENCES `documentManagement.document` (`docId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);
