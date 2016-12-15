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
    ('ServiceLevel_002', 'serviceLevel_002', 'archives', '', false),
    ('logServiceLevel', 'logServiceLevel', 'logs', '', false);


INSERT INTO `recordsManagement.accessRule` (`code`,`duration`, `description`) VALUES
    ('AC01','P10Y', 'Access to accounting documents'),
    ('AC02','P10Y', 'Access to accounting documents'),
    ('AR038', 'P0D', 'Documents administratifs librement communicables. (Code du Patrimoine, art. L. 213-1'),
    ('AR039', 'P25Y', 'Documents dont la communication porte atteinte au secret des délibérations du Gouvernement et des autorités responsables relevant du pouvoir exécutif, à la conduite des relations extérieures, à la monnaie et au crédit public, au secret en matière commerciale et industrielle, à la recherche par les services compétents des infractions fiscales et douanières. (Code du Patrimoine, art. L. 213-2, I, 1, a'),
    ('AR040', 'P25Y', 'Documents qui ne sont pas considérés comme administratifs et mentionnés au dernier alinéa de l''article 1er de la loi n° 78-753 du 17 juillet 1978 : actes et documents élaborés ou détenus par les assemblées parlementaires, avis du Conseil d''Etat et des juridictions administratives, documents de la Cour des comptes mentionnés à l''article L. 140-9 du code des juridictions financières et les documents des chambres régionales des comptes mentionnés à l''article L. 241-6 du même code, documents d''instruction des réclamations adressées au Médiateur de la République, documents préalables à l''élaboration du rapport d''accréditation des établissements de santé prévu à l''article L. 6113-6 du code de la santé publique, rapports d''audit des établissements de santé mentionnés à l''article 40 de la loi de financement de la sécurité sociale pour 2001 (n° 2000-1257 du 23 décembre 2000.'),
    ('AR041', 'P25Y', 'Secret statistique. (Code du Patrimoine, art. L. 213-2, I, 1, a'),
    ('AR042', 'P25Y', 'Documents élaborés dans le cadre d''un contrat de prestation de services exécuté pour le compte d''une ou de plusieurs personnes déterminées. (Code du Patrimoine, art. L. 213-2, I, 1, c'),
    ('AR043', 'P25Y', 'Documents dont la communication est susceptible de porter atteinte au secret médical. (Code du Patrimoine, art. L. 213-2, I, 2'),
    ('AR044', 'P25Y', 'Etat civil (actes de naissance ou de mariage. (Code du Patrimoine, art. L. 213-2, I, 4, e'),
    ('AR045', 'P25Y', 'Minutes et répertoires des officiers publics et ministériels. (Code du Patrimoine, art. L. 213-2, I, 4, d'),
    ('AR046', 'P25Y', 'Documents relatifs aux enquêtes réalisées par les services de la police judiciaire. (Code du Patrimoine, art. L. 213-2, I, 4, b'),
    ('AR047', 'P25Y', 'Documents relatifs aux affaires portées devant les juridictions, sous réserve des dispositions particulières relatives aux jugements, et à l''exécution des décisions de justice. (Code du Patrimoine, art. L. 213-2, I, 4, c'),
    ('AR048', 'P50Y', 'Documents dont la communication porte atteinte à la protection de la vie privée ou portant appréciation ou jugement de valeur sur une personne physique nommément désignée, ou facilement identifiable, ou qui font apparaître le comportement d''une personne dans des conditions susceptibles de lui porter préjudice. (Code du Patrimoine, art. L. 213-2, I, 3'),
    ('AR049', 'P50Y', 'Documents dont la communication porte atteinte au secret de la défense nationale, aux intérêts fondamentaux de l''État dans la conduite de la politique extérieure, à la sûreté de l''État, à la sécurité publique. (Code du Patrimoine, art. L. 213-2, I, 3'),
    ('AR050', 'P50Y', 'Documents relatifs à la construction, à l''équipement et au fonctionnement des ouvrages, bâtiments ou parties de bâtiments utilisés pour la détention de personnes ou recevant habituellement des personnes détenues. (Code du Patrimoine, art. L. 213-2, I, 3'),
    ('AR051', 'P50Y', 'Documents élaborés dans le cadre d''un contrat de prestation de services dont la communication porte atteinte au secret de la défense nationale, aux intérêts fondamentaux de l''État dans la conduite de la politique extérieure, à la sûreté de l''État, à la sécurité publique, à la protection de la vie privée, ou concernant des bâtiments employés pour la détention de personnes ou recevant habituellement des personnes détenues. (Code du Patrimoine, art. L. 213-2, I, 1, c'),
    ('AR052', 'P75Y', 'Secret statistique : données collectées au moyen de questionnaires ayant trait aux faits et aux comportements d''ordre privé. (Code du Patrimoine, art. L. 213-2, I, 4, a'),
    ('AR053', 'P75Y', 'Documents élaborés dans le cadre d''un contrat de prestation de services et pouvant être liés aux services de la police judiciaire ou aux affaires portées devant les juridictions. (Code du Patrimoine, art. L 213-2, I, 1, b'),
    ('AR054', 'P75Y', 'Etat civil (actes de naissance ou de mariage. (Code du Patrimoine, art. L. 213-2, I, 4, e'),
    ('AR055', 'P75Y', 'Minutes et répertoires des officiers publics et ministériels. (Code du Patrimoine, art. L. 213-2, I, 4, d'),
    ('AR056', 'P75Y', 'Documents relatifs aux enquêtes réalisées par les services de la police judiciaire. (Code du Patrimoine, art. L. 213-2, I, 4, b'),
    ('AR057', 'P75Y', 'Documents relatifs aux affaires portées devant les juridictions. (Code du Patrimoine, art. L. 213-2, I, 4, c'),
    ('AR058', 'P100Y', 'Documents évoquant des personnes mineures : statistiques, enquêtes de la police judiciaire, documents relatifs aux affaires portées devant les juridictions et à l''exécution des décisions de justice. (Code du Patrimoine, art. L. 213-2, I, 5'),
    ('AR059', 'P100Y', 'Documents dont la communication est de nature à porter atteinte à l''intimité de la vie sexuelle des personnes : enquêtes de la police judiciaire, documents relatifs aux affaires portées devant les juridictions. (Code du Patrimoine, art. L. 213-2, I, 5'),
    ('AR060', 'P100Y', 'Documents dont la communication est de nature à porter atteinte à l''intimité de la vie sexuelle des personnes : enquêtes de la police judiciaire, documents relatifs aux affaires portées devant les juridictions. (Code du Patrimoine, art. L. 213-2, I, 5'),
    ('AR061', 'P120Y', 'Documents dont la communication est susceptible de porter atteinte au secret médical. (Code du Patrimoine, art. L. 213-2, I, 2'),
    ('AR062', 'P999999999Y', 'Archives publiques dont la communication est susceptible d''entraîner la diffusion d''informations permettant de concevoir, fabriquer, utiliser ou localiser des armes nucléaires, biologiques, chimiques ou toutes autres armes ayant des effets directs ou indirects de destruction d''un niveau analogue. (Code du Patrimoine, art. L. 213-2, II');

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


INSERT INTO `recordsManagement.archivalProfile` (`archivalProfileId`, `reference`, `name`, `descriptionSchema`, `descriptionClass`, `retentionStartDate`, `retentionRuleCode`, `description`, `accessRuleCode`) VALUES
    ('actes', 'actes', 'actes', null, null, null, null, null, null),
    ('invoice', 'invoice', 'Factures', null, 'businessRecords/adminDescription', null, null, null, null);

INSERT INTO `recordsManagement.archiveDescription` (`archivalProfileId`, `fieldName`, `required`) VALUES
    ('invoice', 'companyCode', true),
	('invoice', 'companyName', true),
    ('invoice', 'date', true),
    ('invoice', 'descriptionId', true),
    ('invoice', 'reference', true),
    ('invoice', 'thirdPartyCode', true),
    ('invoice', 'thirdPartyName', true),
    ('invoice', 'thirdPartyTaxIdentifier', false),
    ('invoice', 'natureCode', false),
    ('invoice', 'year', false);

INSERT INTO `recordsManagement.descriptionClass` (`name`,`label`) VALUES
    ('businessRecords/adminDescription', 'adminDescription');