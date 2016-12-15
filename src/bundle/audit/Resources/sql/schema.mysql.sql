DROP TABLE IF EXISTS `audit.event`;

CREATE TABLE `audit.event`
(
  `eventId` VARCHAR(255) NOT NULL,
  `eventDate` timestamp(6) NOT NULL,
  `accountId` text NOT NULL,
  `orgRegNumber` text,
  `orgUnitRegNumber` text,
  `path` text,
  `variables` text DEFAULT NULL,
  `input` text DEFAULT NULL,
  `output` text,
  `status` boolean default true,
  `info` text,
  `instanceName` text,
  PRIMARY KEY (`eventId`)
);