DROP TABLE IF EXISTS `auth.roleMember`;
DROP TABLE IF EXISTS `auth.privilege`;
DROP TABLE IF EXISTS `auth.servicePrivilege`;
DROP TABLE IF EXISTS `auth.role`;
DROP TABLE IF EXISTS `auth.account`;

CREATE TABLE `auth.role`
(
  `roleId` VARCHAR(255),
  `roleName` text NOT NULL,
  `description` text,
  `enabled` boolean DEFAULT true,
  
  PRIMARY KEY (`roleId`)
);

CREATE TABLE `auth.account`
(
  `accountId` VARCHAR(255) NOT NULL,
  `accountName` VARCHAR(255) NOT NULL,
  `displayName` text NOT NULL,
  `accountType` text,
  `emailAddress` text NOT NULL,
  `enabled` boolean DEFAULT true,
  
  `password` text,
  `passwordChangeRequired` boolean DEFAULT true,
  `passwordLastChange` timestamp(6),
  `locked` boolean DEFAULT false,
  `lockDate` timestamp,
  `badPasswordCount` integer,
  `lastLogin` timestamp,
  `lastIp` text,
  `replacingUserAccountId` text,
  `firstName` text,
  `lastName` text,
  `title` text,
  
  `salt` text,
  `tokenDate` timestamp(6),
    
  PRIMARY KEY (`accountId`),
  UNIQUE (`accountName`) 
);

CREATE TABLE `auth.roleMember`
(
  `roleId` VARCHAR(255),
  `userAccountId` VARCHAR(255) NOT NULL,
  FOREIGN KEY (`roleId`)
      REFERENCES `auth.role` (`roleId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  FOREIGN KEY (`userAccountId`)
      REFERENCES `auth.account` (`accountId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  UNIQUE (`roleId`, `userAccountId`)
);

CREATE TABLE `auth.privilege`
(
  `roleId` VARCHAR(255),
  `userStory` VARCHAR(255),
  FOREIGN KEY (`roleId`)
      REFERENCES `auth.role` (`roleId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  UNIQUE (`roleId`, `userStory`)
);


CREATE TABLE `auth.servicePrivilege`
(
  `accountId` VARCHAR(255),
  `serviceURI` VARCHAR(255),
  FOREIGN KEY (`accountId`)
      REFERENCES `auth.account` (`accountId`) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  UNIQUE (`accountId`, `serviceURI`)
);