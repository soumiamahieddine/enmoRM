DROP TABLE IF EXISTS `batchProcessing.scheduling`;
DROP TABLE IF EXISTS `batchProcessing.task`;
DROP TABLE IF EXISTS `batchProcessing.logScheduling`;

-- Table: `batchProcessing.task`

CREATE TABLE `batchProcessing.task`
(
  `taskId` VARCHAR(255) NOT NULL,
  `route` text,
  `description` text,
  PRIMARY KEY (`taskId`)
);

-- Table: `batchProcessing.scheduling`

CREATE TABLE `batchProcessing.scheduling`
(
  `schedulingId` VARCHAR(255) NOT NULL,
  `name` text NOT NULL,
  `taskId` VARCHAR(255) NOT NULL,
  `frequency` text NOT NULL,
  `parameters` text,
  `executedBy` text NOT NULL,
  `lastExecution` timestamp(6),
  `nextExecution` timestamp(6),
  `status` text,
  PRIMARY KEY (`schedulingId`),
  FOREIGN KEY (`taskId`)
    REFERENCES `batchProcessing.task` (`taskId`) MATCH SIMPLE
    ON UPDATE NO ACTION ON DELETE NO ACTION
);

-- Table: `batchProcessing.scheduling`

CREATE TABLE `batchProcessing.logScheduling`
(
  `logId` VARCHAR(255)  NOT NULL,
  `schedulingId` VARCHAR(255)  NOT NULL,
  `executedBy` text NOT NULL,
  `launchedBy` text NOT NULL,
  `logDate` timestamp(6) NOT NULL,
  `status` boolean DEFAULT true,
  `info` text,
  PRIMARY KEY (`logId`)
);
