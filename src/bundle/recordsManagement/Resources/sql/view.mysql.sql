DROP VIEW IF EXISTS `recordsManagement.archiveDigitalResource`;

CREATE VIEW `recordsManagement.archiveDigitalResource` AS
SELECT 
`recordsManagement.archive`.`archiveId`,
`recordsManagement.archive`.`originatorArchiveId`,
`recordsManagement.archive`.`depositorArchiveId`,
`recordsManagement.archive`.`archiveName`,
`recordsManagement.archive`.`originatorOrgRegNumber`,
`recordsManagement.archive`.`originatorOwnerOrgId`,
`recordsManagement.archive`.`depositorOrgRegNumber`,
`recordsManagement.archive`.`archiverOrgRegNumber`,
`recordsManagement.archive`.`status` as `archiveStatus`,
`recordsManagement.archive`.`archivalProfileReference`,
`recordsManagement.archive`.`archivalAgreementReference`,
`recordsManagement.archive`.`finalDisposition`,
`recordsManagement.archive`.`disposalDate`,
`recordsManagement.archive`.`descriptionClass`,
`recordsManagement.archive`.`descriptionId`,
`recordsManagement.archive`.`parentArchiveId`,

`digitalResource.digitalResource`.`resId`,
`digitalResource.digitalResource`.`clusterId`,
`digitalResource.digitalResource`.`size`,
`digitalResource.digitalResource`.`puid`,
`digitalResource.digitalResource`.`mimetype`,
`digitalResource.digitalResource`.`hash`,
`digitalResource.digitalResource`.`hashAlgorithm`,
`digitalResource.digitalResource`.`fileExtension`,
`digitalResource.digitalResource`.`fileName`,
`digitalResource.digitalResource`.`mediaInfo`,
`digitalResource.digitalResource`.`created`,
`digitalResource.digitalResource`.`updated`,

`digitalResource.digitalResource`.`relatedResId`,
`digitalResource.digitalResource`.`relationshipType`

FROM `recordsManagement.archive`
   JOIN `digitalResource.digitalResource` ON `recordsManagement.archive`.`archiveId`=`digitalResource.digitalResource`.`archiveId`;