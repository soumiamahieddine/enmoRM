DELETE FROM `recordsManagement.profileDescription`;
DELETE FROM `recordsManagement.descriptionClass`;
DELETE FROM `recordsManagement.archiveRelationship`;
DELETE FROM `recordsManagement.archive`;

DELETE FROM `recordsManagement.archivalProfile`;
DELETE FROM `recordsManagement.serviceLevel`;

DELETE FROM `recordsManagement.accessRule`;
DELETE FROM `recordsManagement.retentionRule`;


INSERT INTO `recordsManagement.serviceLevel` (`serviceLevelId`, `reference`, `digitalResourceClusterId`, `control`, `default`) VALUES
    ('ServiceLevel_001', 'serviceLevel_001', 'archives', 'formatDetection formatValidation virusCheck convertOnDeposit', true),
    ('ServiceLevel_002', 'serviceLevel_002', 'archives', '', false);


INSERT INTO `recordsManagement.retentionRule` (`code`, `label`, `description`, `duration`, `finalDisposition`) VALUES
    ('compta_1_01','factures','Comptabilité achats / fournisseurs', 'P10Y', 'destruction'),
    ('compta_1_02','justificatifs matières premières','Comptabilité achats / fournisseurs', 'P1Y', 'destruction'),
    ('compta_2_01','factures de ventes','Comptabilité ventes / clients', 'P10Y', 'destruction'),
    ('compta_3_01','doubles factures','Clients litigieux', 'P10Y', 'destruction'),
    ('compta_3_02','relances initiales','Clients litigieux', 'P10Y', 'destruction'),
    ('compta_3_03','courriers','Clients litigieux', 'P10Y', 'destruction'),
    ('compta_3_04','mise en demeure','Clients litigieux', 'P10Y', 'destruction'),
    ('compta_3_05','échanges avec avocats','Clients litigieux', 'P10Y', 'destruction'),
    ('compta_4_01','Journaux','Comptabilité générale / Editions comptables', 'P10Y', 'destruction'),
    ('compta_4_02','Grands livres généraux et auxiliarisés','Comptabilité générale / Editions comptables', 'P10Y', 'destruction'),
    ('compta_4_03','Balances générales et auxiliaires','Comptabilité générale / Editions comptables', 'P10Y', 'destruction'),
    ('compta_4_04','Journaux centralisateurs','Comptabilité générale / Editions comptables', 'P10Y', 'destruction'),
    ('compta_5_01','relevés bancaires','Trésorerie', 'P10Y', 'destruction'),
    ('compta_5_02','pièces bancaires-relevés de frais','Trésorerie', 'P10Y', 'destruction'),
    ('compta_5_03','souches des chéquiers','Trésorerie', 'P10Y', 'destruction'),
    ('compta_5_04','journaux de caisse (cahiers','Trésorerie', 'P10Y', 'destruction'),
    ('compta_5_05','justificatifs de paiements divers','Trésorerie', 'P10Y', 'destruction'),
    ('compta_5_06','traites / encaissements clients','Trésorerie', 'P10Y', 'destruction'),
    ('compta_6_01','dossier annuel entrées','Immobilisations', 'P30Y', 'destruction'),
    ('compta_6_02','dossier annuel sorties','Immobilisations', 'P30Y', 'destruction'),
    ('compta_6_03','éditions des amortissements','Immobilisations', 'P10Y', 'destruction'),
    ('compta_7_01','déclarations de TVA et justificatifs','Fiscalité', 'P6Y', 'destruction'),
    ('compta_7_02','déclarations de prorata de TVA','Fiscalité', 'P6Y', 'destruction'),
    ('compta_7_03','déclarations IS (intégration fiscale et liasses fiscales','Fiscalité', 'P10Y', 'destruction'),
    ('compta_7_04','déclarations TP (Taxe professionnelle','Fiscalité', 'P3Y', 'destruction'),
    ('compta_7_05','rôles des taxes foncières','Fiscalité', 'P999999999Y', 'destruction'),
    ('compta_7_06','modèles U, P, IL (taxe foncière - justificatifs de modifications','Fiscalité', 'P999999999Y', 'destruction'),
    ('compta_7_07','DADS2 (déclaration annuelle des données sociales','Fiscalité', 'P10Y', 'destruction'),
    ('compta_7_08','déclarations de précompte mobilier','Fiscalité', 'P3Y', 'destruction'),
    ('compta_7_09','TVTS (taxe sur les véhicules de tourisme des sociétés','Fiscalité', 'P4Y', 'destruction'),
    ('compta_7_10','TGAP (taxe générale des activités polluantes et états de contrôle','Fiscalité', 'P3Y', 'destruction'),
    ('compta_7_11','CSSS (Contribution sociale de solidarité des sociétés','Fiscalité', 'P10Y', 'destruction'),
    ('compta_7_12','demande de récupération TIPP (taxe intérieure produits pétroliers','Fiscalité', 'P3Y', 'destruction'),
    ('compta_7_13','DEB (Déclarations d''échanges de biens','Fiscalité', 'P3Y', 'destruction'),
    ('compta_8_01','justificatifs de comptes de bilan','Comptes de bilan et clôture d''exercice', 'P10Y', 'destruction'),
    ('compta_8_02','justificatifs des charges et produits','Comptes de bilan et clôture d''exercice', 'P10Y', 'destruction'),
    ('compta_8_03','tableaux récaptiulatifs des déclarations','Comptes de bilan et clôture d''exercice', 'P10Y', 'destruction'),
    ('compta_8_04','liasse fiscale','Comptes de bilan et clôture d''exercice', 'P10Y', 'destruction'),
    ('compta_8_05','annexe sociale','Comptes de bilan et clôture d''exercice', 'P10Y', 'destruction'),
    ('compta_8_06','bilans et annexes sociales','Comptes de bilan et clôture d''exercice', 'P10Y', 'destruction'),
    ('compta_8_07','documents prévisionnels','Comptes de bilan et clôture d''exercice', 'P10Y', 'destruction'),
    ('compta_9_01','bilan de fusion','Opérations de restructuration', 'P10Y', 'destruction'),
    ('compta_9_02','liste des immobilisations','Opérations de restructuration', 'P10Y', 'destruction'),
    ('compta_9_03','courriers','Opérations de restructuration', 'P10Y', 'destruction');


INSERT INTO `recordsManagement.accessRule` (`code`,`duration`, `description`) VALUES
    ('COM_INTERNE','P1M','Communication interne'),
    ('FACTURE_CLIENT','P1Y','Facture client');

INSERT INTO `recordsManagement.archivalProfile` (`archivalProfileId`, `reference`, `name`, `descriptionSchema`, `descriptionClass`, `retentionStartDate`, `retentionRuleCode`, `description`, `accessRuleCode`) VALUES
    ('actes', 'actes', 'actes', null, null, null, null, null, null),
    ('invoice', 'invoice', 'Factures', null, 'businessRecords/adminDescription', null, null, null, null);

INSERT INTO `recordsManagement.descriptionField` (`name`, `label`, `type`, `default`, `minLength`, `maxLength`, `minValue`, `maxValue`, `enumeration`, `pattern`) VALUES
    ('num_facture', 'Numéro de facture', 'name', null, null, null, null, null, null, null),
    ('client', 'Client', 'text', null, null, null, null, null, null, null),
    ('date_facture', 'Date de la facture', 'date', null, null, null, null, null, null, null);


INSERT INTO `recordsManagement.documentProfile` (`archivalProfileId`, `documentProfileId`, `reference`, `name`, `required`, `acceptUserIndex`) VALUES
    ('FACTURES_CLIENTS', 'DOCUMENT_PROFILE_01', 'FACTURES_CLIENTS', 'Factures', true, false);

INSERT INTO `recordsManagement.archiveDescription` (`archivalProfileId`, `fieldName`, `required`, `position`) VALUES
    ('FACTURES_CLIENTS', 'num_facture', true, 0),
    ('FACTURES_CLIENTS', 'client', false, 1),
    ('FACTURES_CLIENTS', 'date_facture', true, 2);
