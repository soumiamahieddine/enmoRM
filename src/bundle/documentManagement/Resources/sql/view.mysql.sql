DROP VIEW IF EXISTS `documentManagement.archiveDocument`;

CREATE VIEW `documentManagement.archiveDocument` AS 
 SELECT `documentManagement.document`.`docId`,
    `documentManagement.document`.`archiveId`,
    `documentManagement.document`.`type`,
    `documentManagement.document`.`control`,
    `documentManagement.document`.`copy`,
    `documentManagement.document`.`description`,
    `documentManagement.document`.`language`,
    `documentManagement.document`.`purpose`,
    `documentManagement.document`.`title`,
    `documentManagement.document`.`creator`,
    `documentManagement.document`.`publisher`,
    `documentManagement.document`.`contributor`,
    `documentManagement.document`.`category`,
    `documentManagement.document`.`available`,
    `documentManagement.document`.`valid`,
    `documentManagement.document`.`depositorDocId`,
    `documentManagement.document`.`originatorDocId`,
	
    `documentManagement.document`.`creation`,
    `documentManagement.document`.`issue`,
    `documentManagement.document`.`receipt`,
    `documentManagement.document`.`response`,
    `documentManagement.document`.`submission`,
	
    `recordsManagement.archive`.`originatorArchiveId`,
    `recordsManagement.archive`.`depositorArchiveId`,
    `recordsManagement.archive`.`originatorOrgRegNumber`,
    `recordsManagement.archive`.`originatorOwnerOrgId`,
    `recordsManagement.archive`.`depositorOrgRegNumber`,
    `recordsManagement.archive`.`archiverOrgRegNumber`,
    `recordsManagement.archive`.`status` as `archiveStatus`,
    `recordsManagement.archive`.`archivalProfileReference`,
    `recordsManagement.archive`.`archivalAgreementReference`,
    `recordsManagement.archive`.`finalDisposition`,
    `recordsManagement.archive`.`disposalDate`,
    `recordsManagement.archive`.`parentArchiveId`,
    `recordsManagement.archive`.`archiveName`
   FROM `recordsManagement.archive`
     LEFT JOIN `documentManagement.document` ON `recordsManagement.archive`.`archiveId`=`documentManagement.document`.`archiveId`;