DROP TABLE IF EXISTS `lifeCycle.event`;
DROP TABLE IF EXISTS `lifeCycle.eventFormat`;

CREATE TABLE `lifeCycle.event`
(
  `eventId` varchar(255) NOT NULL,
  `eventType` text NOT NULL,
  `timestamp` timestamp(6) NOT NULL,
  `instanceName` text NOT NULL,
  `orgRegNumber` text,
  `orgUnitRegNumber` text,
  `accountId` text,
  `objectClass` text NOT NULL,
  `objectId` text NOT NULL,
  `operationResult` boolean,
  `description` text,
  `eventInfo` text,
  PRIMARY KEY (`eventId`)
);


CREATE TABLE `lifeCycle.eventFormat`
(
  `type` varchar(255) NOT NULL,
  `format` text NOT NULL,
  `message` text NOT NULL,
  `notification` boolean DEFAULT false,
  PRIMARY KEY (`type`)
);
