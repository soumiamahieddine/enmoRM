DELETE FROM `medona.archivalAgreement`;

INSERT INTO `medona.archivalAgreement` (`archivalAgreementId`, `reference`, `name`, `description`, `archiverOrgRegNumber`, `depositorOrgRegNumber`, `originatorOrgIds`, `beginDate`, `endDate`, `allowedFormats`, `enabled`, `archivalProfileReference`, `serviceLevelReference`, `maxSizeTransfer`, `maxSizeDay`, `maxSizeWeek`, `maxSizeMonth`, `maxSizeYear`, `maxSizeAgreement`, `signed`, `autoTransferAcceptance`, `processSmallArchive`) VALUES
('MAARCH_SAS_invoice', 'MAARCH_SAS_invoice', 'Maarch SAS','invoice', 'org_123456789_Archives', 'org_45239273100025_Finance', 'MAARCH_SAS_finance', null, null, '', true, 'invoice', 'serviceLevel_001', null, null, null, null, null, 1000000, false, true, false),
('MAARCH_WA_invoice', 'MAARCH_WA_invoice', 'Maarch WA','invoice', 'org_123456789_Archives', 'org_45239273100026_Finance', 'MAARCH_WA_finance', null, null, '', true, 'invoice', 'serviceLevel_002', null, null, null, null, null, 1000000, false, true, false),

('MAARCH_LES_BAINS_ACTES', 'MAARCH_LES_BAINS_ACTES', 'Maarch les Bains','Actes', 'org_123456789_Archives', 'org_987654321_Versant', 'MAARCH_LES_BAINS_SJA MAARCH_LES_BAINS_SF',null, null, 'fmt/18 fmt/95 fmt/354 fmt/476 fmt/477 fmt/478 fmt/479 fmt/480 fmt/481 fmt/412 fmt/189 fmt/101', true, 'actes', 'serviceLevel_001', null, null, null, null, null, 1000000, false, true, true);
