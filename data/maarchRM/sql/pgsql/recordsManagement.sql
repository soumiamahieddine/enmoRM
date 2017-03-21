TRUNCATE TABLE "recordsManagement"."archiveRelationship" CASCADE;
TRUNCATE TABLE "recordsManagement"."archive" CASCADE;

TRUNCATE TABLE "recordsManagement"."archivalProfile" CASCADE;
TRUNCATE TABLE "recordsManagement"."serviceLevel" CASCADE;

TRUNCATE TABLE "recordsManagement"."accessRule" CASCADE;
TRUNCATE TABLE "recordsManagement"."retentionRule" CASCADE;

TRUNCATE TABLE "recordsManagement"."descriptionField" CASCADE;

INSERT INTO "recordsManagement"."serviceLevel" ("serviceLevelId", "reference", "digitalResourceClusterId", "control", "default") VALUES
    ('ServiceLevel_001', 'serviceLevel_001', 'archives', 'formatDetection formatValidation virusCheck convertOnDeposit', true),
    ('ServiceLevel_002', 'serviceLevel_002', 'archives', '', false);


INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES
    -- Ressources Humaines 
	('BulletinsDePaie', 'Bulletins de paie', 'Code du Travail, art. L3243-4 - Code de la Sécurité Sociale, art. L243-12', 'P5Y', 'destruction'),
	('DossierDuPersonnel', 'Dossier du personnel', 'Convention Collective nationale de retraite et de prévoyance des cadres, art. 23', 'P90Y', 'destruction'),

	-- Commerce
	('DocumentComptables', 'Documents comptables', 'Code du commerce, Article L123-22', 'P10Y', 'destruction'),
	-- Fiscalité
	('ControleImpot', 'Contrôle de l''impôt', 'Livre des Procédures Fiscales, art 102 B et L 169 : Livres, registres, documents ou pièces sur lesquels peuvent s''exercer les droits de communication, d''enquête et de contrôle de l''administration', 'P6Y', 'destruction'),
	('ImpotSociete', 'Impôt sur les sociétés et liasses fiscales', 'Livre des Procédures Fiscales, art 102 B et L 169 alinea 2: Les registres tenus en application du 9 de l''article 298 sexdecies F du code général des impôts et du 5 de l''article 298 sexdecies G du même code', 'P10Y', 'destruction'),
	('ImpotsAutres', 'Taxe professionnelle', 'Livre des Procédures Fiscales, art 102 B et L 169 alinea 3', 'P3Y', 'destruction');

INSERT INTO "recordsManagement"."accessRule" ("code", "duration", "description") VALUES
    ('DossierDuPersonnel','P999Y','Dossier du personnel'),
    ('ComptabiliteAchats','P999Y','Comptabilité achats'),
	('ComptabiliteVente','P999Y','Comptabilité vente');

INSERT INTO "recordsManagement"."accessEntry" ("accessRuleCode", "orgRegNumber", "originatorAccess") VALUES
    ('ComptabiliteAchats','regNum_CPTFOUR',TRUE),
	('ComptabiliteVente','regNum_CPTCLI',TRUE),
	('DossierDuPersonnel','regNum_RH',TRUE),
	('BulletinsDePaie','regNum_PAIE',TRUE);

INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode") VALUES
    ('FacturesClients', 'FacturesClients', 'Factures clients', null, null, 'description/DateFacture', 'DocumentComptables', 'Factures de vente clients', 'ComptabiliteVente'),
	('FacturesFournisseurs', 'FacturesFournisseurs', 'Factures fournisseurs', null, null, 'depositDate', 'DocumentComptables', 'Factures d''achats fournisseurs', 'ComptabiliteAchats');

INSERT INTO "recordsManagement"."descriptionField" ("name", "label", "type", "default", "minLength", "maxLength", "minValue", "maxValue", "enumeration", "pattern") VALUES
    ('NumeroFacture', 'Numéro de facture', 'name', null, null, null, null, null, null, null),
    ('Client', 'Client', 'text', null, null, null, null, null, null, null),
    ('DateFacture', 'Date de la facture', 'date', null, null, null, null, null, null, null);

INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES
    ('FacturesClients', 'NumeroFacture', true, 0),
    ('FacturesClients', 'Client', false, 1),
    ('FacturesClients', 'DateFacture', true, 2);
