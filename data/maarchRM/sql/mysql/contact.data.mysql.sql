DELETE FROM `contact.communication`;
DELETE FROM `contact.address`;
DELETE FROM `contact.contact`;
DELETE FROM `contact.communicationMean`;

-- communicationMean --
INSERT INTO `contact.communicationMean` (`code`, `name`, `enabled`) VALUES
    ('TE','Téléphone',true),
	('AL','Téléphone mobile',true),
    ('FX','Fax',true),
    ('AO','URL',true),
    ('AU','FTP',true),
    ('EM','E-mail',true),
    ('AH','World Wide Web',false);
	
-- contact --
INSERT INTO `contact.contact`
(`contactId`, `contactType`, `orgName`, `firstName`, `lastName`, `title`, `function`, `service`, `displayName`)
    VALUES 
('Bblier_ACME', 'person', null, 'Bernard', 'BLIER', 'M.', 'Responsable du service de tiers-archivage', 'service versant', 'Bernard BLIER'),
('ACME', 'organization', 'ACME', NULL, NULL, NULL, NULL, NULL, 'ACME'),
('MAARCH_SAS', 'organization', 'MAARCH SAS', NULL, NULL, NULL, NULL, NULL, 'MAARCH SAS'),
('MAARCH_WA', 'organization', 'MAARCH WEST AFRICA', NULL, NULL, NULL, NULL, NULL, 'MAARCH WEST AFRICA'),
('MAARCH_LES_BAINS_SV', 'organization', 'MAARCH LES BAINS SV', NULL, NULL, NULL, NULL, NULL, 'Service Versant'),
('MAARCH_LES_BAINS_SF', 'organization', 'MAARCH LES BAINS SF', NULL, NULL, NULL, NULL, NULL, 'Service Finance'),
('MAARCH_LES_BAINS_SJA', 'organization', 'MAARCH LES BAINS SJA', NULL, NULL, NULL, NULL, NULL, 'Service Juridique et Administratif');

-- address --
INSERT INTO `contact.address`
(`addressId`,`contactId`, `purpose`, `room`, `floor`, `building`, `number`, `street`, `postBox`, `block`, `citySubDivision`, `postCode`, `city`, `country`)
    VALUES 
('Bblier_ACME_1', 'Bblier_ACME', 'main', '', '', '', '42', 'rue des Jardins', '', '', '', '92000', 'Nanterre', 'FR'),
('ACME_1', 'ACME', 'main', '', '', '', '42', 'rue des Jardins', '', '', '', '92000', 'Nanterre', 'FR'),
('MAARCH_SAS_1', 'MAARCH_SAS', 'main', '', '', '', '50', 'rue de la République', '', '', '', '54300', 'Lunéville', 'FR'),
('MAARCH_WA_1', 'MAARCH_WA', 'main', '', '', '', '221B', 'Baker Street', '', '', '', '647', 'Lagos', 'NG'),
('MAARCH_LES_BAINS_SV_1', 'MAARCH_LES_BAINS_SV', 'main', '', '', '', '71', 'rue Banaudon', '', '', '', '69009', 'Lyon', 'FR'),
('MAARCH_LES_BAINS_SF_1', 'MAARCH_LES_BAINS_SF', 'main', '', '', '', '33', 'boulevard de Prague', '', '', '', '79000', 'Niort', 'FR'),
('MAARCH_LES_BAINS_SJA_1', 'MAARCH_LES_BAINS_SJA', 'main', '', '', '', '90', 'rue de Groussay', '', '', '', '12000', 'Rodez', 'FR');

-- communication --
INSERT INTO `contact.communication` 
(`communicationId`, `contactId`, `purpose`, `comMeanCode`, `value`, `info`)
    VALUES 
('01', 'ACME', 'main', 'TE', '04 92 96 80 80', null),
('02', 'ACME', 'main', 'FX', '04 92 96 92 96', null),
('03', 'ACME', 'main', 'EM', 'acme@maarg.org', null);