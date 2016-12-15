-- Schema: contact

DROP TABLE IF EXISTS `contact.communication`;
DROP TABLE IF EXISTS `contact.communicationMean`;
DROP TABLE IF EXISTS `contact.address`;
DROP TABLE IF EXISTS `contact.contact`;


-- Table: `contact.contact`

CREATE TABLE `contact.contact`
(
  `contactId` VARCHAR(255) NOT NULL,
  `contactType` VARCHAR(255) DEFAULT 'person',
  `orgName` text,
  `firstName` text,
  `lastName` text,
  `title` text,
  `function` text,
  `service` text,
  `displayName` text,
  PRIMARY KEY (`contactId`)
);

-- Table: `contact`.address

CREATE TABLE `contact.address`
(
  `addressId` VARCHAR(255) NOT NULL,
  `contactId` VARCHAR(255) NOT NULL,
  `purpose` VARCHAR(255) NOT NULL,
  `room` text,
  `floor` text,
  `building` text,
  `number` text,
  `street` text,
  `postBox` text,
  `block` text,
  `citySubDivision` text,
  `postCode` text,
  `city` text,
  `country` text,
  UNIQUE (`contactId`, `purpose`),
  PRIMARY KEY (`addressId`),
  FOREIGN KEY (`contactId`) 
    REFERENCES `contact.contact` (`contactId`) MATCH SIMPLE
	ON UPDATE NO ACTION ON DELETE NO ACTION
);


-- Table: `contact.communicationMean`

CREATE TABLE `contact.communicationMean`
(
  `code` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `enabled` boolean,
  PRIMARY KEY (`code`),
  UNIQUE (`name`)
);

-- Table: `contact.communication`

CREATE TABLE `contact.communication`
(
  `communicationId` VARCHAR(255) NOT NULL,
  `contactId` VARCHAR(255) NOT NULL,
  `purpose` VARCHAR(255) NOT NULL,
  `comMeanCode` VARCHAR(255) NOT NULL,
  `value` text NOT NULL,
  `info` text,
  PRIMARY KEY (`communicationId`),
  UNIQUE (`contactId`, `purpose`, `comMeanCode`),
  FOREIGN KEY (`contactId`) 
    REFERENCES `contact.contact` (`contactId`) MATCH SIMPLE
	ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY (`comMeanCode`)
      REFERENCES `contact.communicationMean` (`code`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
);



