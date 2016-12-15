DELETE FROM `organization.orgContact`;
DELETE FROM `organization.userPosition`;
UPDATE `organization.organization` SET `parentOrgId` = null, `ownerOrgId` = null;
DELETE FROM `organization.organization`;
DELETE FROM `organization.orgRole`;
DELETE FROM `organization.orgType`;
DELETE FROM `organization.servicePosition`;



# Organization unit types #
INSERT INTO `organization.orgType`(`code`, `name`) VALUES 
('Collectivité', 'Collectivité'),
('Société', 'Société'),
('Direction', 'Direction d''une entreprise ou d''une collectivité'),
('Service', 'Service d''une entreprise ou d''une collectivité'),
('Division', 'Division d''une entreprise');

# `organization`.orgRole #
INSERT INTO `organization.orgRole` (`code`, `name`,`description`) VALUES
('owner','organization/owner', 'The system owner'),
('depositor','recordsManagement/depositor', 'Archive depositor agency'),
('archiver','recordsManagement/archiver', 'Archival agency'),
('originator','recordsManagement/originator', 'Archive originating agency'),
('controlAuthority','recordsManagement/controlAuthority', 'Archive control authority'),
('requester','recordsManagement/requester', 'Archive requester');

# Organizations #
INSERT INTO `organization.organization`( `orgId`, `orgName`, `displayName`, `parentOrgId`, `ownerOrgId`, `orgRoleCodes`, `registrationNumber`, `isOrgUnit`) VALUES 
('ACME', 'ACME','Archivage Conversation et Mémoire Electronique', NULL, NULL, NULL, 'org_123456789', false),
('ACME_Archive', 'Service d''Archives','Service d''Archives', 'ACME', 'ACME', 'archiver owner', 'org_123456789_Archives', true),

('MAARCH', 'Maarch','Maarch', NULL, NULL, NULL, 'org_452392731', false),
('MAARCH_SAS', 'Maarch SAS','Maarch SAS', 'MAARCH', 'MAARCH', NULL, 'org_45239273100025', false),
('MAARCH_SAS_finance', 'Service finance','Service finance', 'MAARCH_SAS', 'MAARCH_SAS', 'depositor originator', 'org_45239273100025_Finance', true),
('MAARCH_WA', 'Maarch West Africa','Maarch West Africa', 'MAARCH', 'MAARCH', NULL, 'org_45239273100026', false),
('MAARCH_WA_finance', 'Service finance','Service finance', 'MAARCH_WA', 'MAARCH_WA', 'depositor originator', 'org_45239273100026_Finance', true),
('MAARCH_DG', 'Direction Générale Groupe','Direction Générale Groupe', 'MAARCH', 'MAARCH', NULL, 'org_452392731_DG', true),

('MAARCH_LES_BAINS', 'Maarch les Bains','Maarch les Bains', NULL, NULL, NULL, 'org_987654321', false),
('MAARCH_LES_BAINS_DGS', 'Direction Générale des Services','Direction Générale des Services', 'MAARCH_LES_BAINS', 'MAARCH_LES_BAINS', NULL, 'org_987654321_DGS', true),
('MAARCH_LES_BAINS_SF', 'Service Finance','Service Finance', 'MAARCH_LES_BAINS_DGS', 'MAARCH_LES_BAINS', 'originator', 'org_987654321_DGS_SF', true),
('MAARCH_LES_BAINS_SJA', 'Service Juridique et Administratif','Service Juridique et Administratif', 'MAARCH_LES_BAINS_DGS', 'MAARCH_LES_BAINS', 'originator', 'org_987654321_DGS_SJA', true),
('MAARCH_LES_BAINS_DAC', 'Direction des Affaires Culturelles','Direction des Affaires Culturelles', 'MAARCH_LES_BAINS_DGS', 'MAARCH_LES_BAINS', NULL, 'org_987654321_DGS_DAC', true),
('MAARCH_LES_BAINS_SV', 'Service Versant','Service Versant', 'MAARCH_LES_BAINS_DAC', 'MAARCH_LES_BAINS', 'depositor', 'org_987654321_Versant', true),

('CD06','Conseil départemental des Alpes Maritimes', 'CD06',NULL, NULL, NULL,'org_111222333', false),
('AD06', 'Archives départementales des Alpes Maritimes','AD06', 'CD06','CD06','controlAuthority', 'org_111222333_Controle', true);

# Organization user position #
INSERT INTO `organization.userPosition` (`userAccountId`, `orgId`, `function`, `default`) VALUES
('bblier', 'ACME_Archive', 'Archiviste', true),

('aastier', 'MAARCH_SAS_finance', '', true),
('rreynolds', 'MAARCH_WA_finance', '', true),
('aadams', 'MAARCH_DG', '', true),

('ppreboist', 'MAARCH_LES_BAINS_DGS', '', true),
('ccox', 'MAARCH_LES_BAINS_SF', '', true),
('sstone', 'MAARCH_LES_BAINS_SJA', '', true),
('cchaplin', 'MAARCH_LES_BAINS_SV', '', true),

('sstallone', 'AD06', '', true);

# Organization service position #
INSERT INTO `organization.servicePosition` (`serviceAccountId`, `orgId`) VALUES
('Maarch_Service', 'ACME_Archive');

# Organization contacts #
INSERT INTO `organization.orgContact` (`contactId`, `orgId`, `isSelf`) VALUES
('ACME', 'ACME_Archive', true),
('Bblier_ACME', 'ACME_Archive', false),
('MAARCH_SAS', 'MAARCH_SAS_finance', true),
('MAARCH_WA', 'MAARCH_WA_finance', true),
('MAARCH_LES_BAINS_SV', 'MAARCH_LES_BAINS_SV', true),
('MAARCH_LES_BAINS_SF', 'MAARCH_LES_BAINS_SF', true),
('MAARCH_LES_BAINS_SJA', 'MAARCH_LES_BAINS_SJA', true);
