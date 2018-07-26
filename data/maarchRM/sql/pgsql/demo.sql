-- Insert preProcess SQL queries here
SET CLIENT_ENCODING TO 'UTF8';

-- AUTH
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES
('user', 'superadmin', 'Super', 'Admin', 'M.', 'super admin', 'superadmin', 'info@maarch.org', '186cf774c97b60a1c106ef718d10970a6a06e06bef89553d9ae65d938a886eae',true,false,null,false,0,null,null,null);


-- ROLE
INSERT INTO "auth"."role"("roleId", "roleName", "description", "enabled") VALUES
    ('ADMIN', 'Administrateur', 'Groupe administrateur', true),
    ('CORRESPONDANT_ARCHIVES', 'Archiviste', 'Correspondant d''archives', true),
    ('UTILISATEUR', 'Utilisateur', 'Groupe utilisateur', true); 

-- PRIVILEGE
INSERT INTO "auth"."privilege"("roleId", "userStory") VALUES
    ('ADMIN', 'adminTech/*'),
    ('ADMIN', 'adminFunc/AdminArchivalProfileAccess'),
    ('ADMIN', 'adminFunc/adminAuthorization'),
    ('ADMIN', 'adminFunc/adminOrgContact'),
    ('ADMIN', 'adminFunc/adminOrgUser'),
    ('ADMIN', 'adminFunc/adminOrganization'),
    ('ADMIN', 'adminFunc/adminServiceaccount'),
    ('ADMIN', 'adminFunc/adminUseraccount'),
    ('ADMIN', 'adminFunc/contact'),
    ('ADMIN', 'journal/audit'),
    
    ('CORRESPONDANT_ARCHIVES', 'adminArchive/*'),
    ('CORRESPONDANT_ARCHIVES', 'archiveRetrieval/*'),
    ('CORRESPONDANT_ARCHIVES', 'archiveManagement/*'),
    ('CORRESPONDANT_ARCHIVES', 'archiveDeposit/*'),
    ('CORRESPONDANT_ARCHIVES', 'adminFunc/batchScheduling'),
    ('CORRESPONDANT_ARCHIVES', 'journal/lifeCycleJournal'),
    ('CORRESPONDANT_ARCHIVES', 'journal/searchLogArchive'),
    ('CORRESPONDANT_ARCHIVES', 'adminFunc/AdminArchivalProfileAccess'),
    ('CORRESPONDANT_ARCHIVES', 'adminFunc/adminOrgContact'),
    ('CORRESPONDANT_ARCHIVES', 'adminFunc/adminOrgUser'),
    ('CORRESPONDANT_ARCHIVES', 'adminFunc/adminOrganization'),
    ('CORRESPONDANT_ARCHIVES', 'destruction/*'),

    ('UTILISATEUR', 'archiveRetrieval/*'),
    ('UTILISATEUR', 'archiveDeposit/*'),
    ('UTILISATEUR', 'archiveManagement/modify'),
    ('UTILISATEUR', 'archiveManagement/filePlan');


-- roleMember
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES
('ADMIN', 'superadmin');

    
-- LIFECYCLE
INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
('recordsManagement/accessRuleModification', 'resId hashAlgorithm hash address accessRuleStartDate accessRuleDuration previousAccessRuleStartDate previousAccessRuleDuration originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification de la règle de communicabilité de l''archive %6$s'),
('recordsManagement/addRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relation ajoutée avec l''archive %6$s'),
('recordsManagement/archivalProfileModification', 'archivalProfileReference', FALSE, 'Modification du profil %6$s.'),
('recordsManagement/consultation', 'resId hash hashAlgorith address size', FALSE, 'Consultation de la ressource %9$s'),
('recordsManagement/conversion', 'resId hashAlgorithm hash address convertedResId convertedHashAlgorithm convertedHash convertedAddress software docId size', FALSE, 'Conversion du document %18$s'),
('recordsManagement/deleteRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relation avec l''archive %6$s supprimée'),
('recordsManagement/delivery', 'resId hashAlgorithm hash address requesterOrgRegNumber archiverOrgRegNumber size', FALSE, 'Communication de l''archive %6$s'),
('recordsManagement/deposit', 'resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber format size', FALSE, 'Dépôt de l''archive %6$s'),
('recordsManagement/descriptionModification','property', FALSE, 'Modification des métadonnées de l''archive %6$s.'),
('recordsManagement/destructionRequest', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Demande de destruction de l''archive %6$s'),
('recordsManagement/destructionRequestCanceling', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Annulation de la demande de destruction de l''archive %6$s'),
('recordsManagement/destruction', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Destruction de l''archive %6$s'),
('recordsManagement/freeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Gel de l''archive %6$s'),
('recordsManagement/integrityCheck', 'resId hash hashAlgorithm address requesterOrgRegNumber info', FALSE, 'Validation d''intégrité'),
('recordsManagement/metadataModification', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification des métadonnées de l''archive %6$s'),
('recordsManagement/profileCreation', 'archivalProfileReference', FALSE, 'Création du profil %6$s'),
('recordsManagement/profileDestruction', 'archivalProfileReference', FALSE, 'Destruction du profil %6$s'),
('recordsManagement/periodicIntegrityCheck', 'startDatetime endDatetime nbArchivesToCheck nbArchivesInSample archivesChecked', FALSE, 'Validation périodique de l''intégrité'),
('recordsManagement/restitution', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Restitution de l''archive %6$s'),
('recordsManagement/retentionRuleModification', 'resId hashAlgorithm hash address retentionStartDate retentionDuration finalDisposition previousStartDate previousDuration previousFinalDisposition originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification de la règle de conservation de l''archive %6$s'),
('recordsManagement/unfreeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Dégel de l''archive %6$s');

INSERT INTO "recordsManagement"."serviceLevel" ("serviceLevelId", "reference", "digitalResourceClusterId", "control", "default", "samplingFrequency","samplingRate") VALUES
    ('ServiceLevel_001', 'serviceLevel_001', 'archives', 'formatDetection formatValidation virusCheck convertOnDeposit', false, 2, 50),
    ('ServiceLevel_002', 'serviceLevel_002', 'archives', '', true,2 ,50);


INSERT INTO "batchProcessing"."scheduling"
("schedulingId", "name", "executedBy", "taskId", "frequency","parameters","lastExecution","nextExecution","status") VALUES
('chainJournalAudit', 'Chaînage audit', 'System', '01', '00;00;;;;;;;',null,null,null,'paused'),
('chainJournalLifeCycle', 'Chaînage du journal du cycle de vie', 'System', '02', '00;01;;;;;;;', null,null,null,'paused'),
('integrity', 'Intégrité', 'System','03', '00;02;;;;;;;',null,null,null,'paused'),
('deleteArchive', 'Destruction', 'System', '04', '00;03;;;;;;;', null,null,null,'paused'),
('sendNotification', 'Envoi des notifications', 'System', '05', '00;04;;;;;;;', null,null,null,'paused');


INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES ('BULPAI', 'Bulletins de paie', 'Code du Travail, art. L3243-4 - Code de la Sécurité Sociale, art. L243-12', 'P5Y', 'destruction');
INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES ('DIP', 'Dossier individuel du personnel', 'Convention Collective nationale de retraite et de prévoyance des cadres, art. 23', 'P90Y', 'destruction');
INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES  ('COM', 'Documents comptables', 'Code du commerce, Article L123-22', 'P10Y', 'destruction');
INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES ('IMP', 'Contrôle de l''impôt', 'Livre des Procédures Fiscales, art 102 B et L 169 : Livres, registres, documents ou pièces sur lesquels peuvent s''exercer les droits de communication, d''enquête et de contrôle de l''administration', 'P6Y', 'destruction');
INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES ('IMPS', 'Impôt sur les sociétés et liasses fiscales', 'Livre des Procédures Fiscales, art 102 B et L 169 alinea 2: Les registres tenus en application du 9 de l''article 298 sexdecies F du code général des impôts et du 5 de l''article 298 sexdecies G du même code', 'P10Y', 'destruction');
INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES ('IMPA', 'Taxe professionnelle', 'Livre des Procédures Fiscales, art 102 B et L 169 alinea 3', 'P3Y', 'destruction');
INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES ('GES', 'Documents de gestion', 'Documents de gestion', 'P5Y', 'destruction');

INSERT INTO "digitalResource"."cluster" ("clusterId",  "clusterName", "clusterDescription") VALUES
    ('archives', 'Digital_resource_cluster_for_archives', 'Digital resource cluster for archives');


INSERT INTO "digitalResource"."repository" ("repositoryId", "repositoryName", "repositoryReference", "repositoryType", "repositoryUri", "enabled") VALUES
    ('archives_1', 'Digital resource repository for archives', 'repository_1', 'fileSystem', '/var/www/laabs/data/maarchRM/repository/archives_1', true),
    ('archives_2', 'Digital resource repository for archives 2', 'repository_2', 'fileSystem', '/var/www/laabs/data/maarchRM/repository/archives_2', true);


INSERT INTO "digitalResource"."clusterRepository" ("clusterId", "repositoryId", "readPriority", "writePriority", "deletePriority") VALUES
    ('archives', 'archives_1', 1, 1, 1),
    ('archives', 'archives_2', 2, 1, 2);


INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('ACME', 'Archives Conservation et Mémoire Électronique', 'Archives Conservation et Mémoire Électronique', NULL, NULL, NULL, 'ACME',false);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('DAF', 'Direction Administrative et Financière', 'Direction Administrative et Financière', 'ACME', 'ACME', NULL, 'DAF',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('CG', 'Comptabilité Générale', 'Comptabilité Générale', 'DAF', 'ACME', NULL, 'CG',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('AC', 'Agence comptable', 'Agence comptable', 'DAF', 'ACME', NULL, 'AC',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('DAM', 'Direction Administrative et du Matériel', 'Direction Administrative et du Matériel', 'ACME', 'ACME', NULL, 'DAM',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('SAM', 'Service Administratif et du Matériel', 'Service Administratif et du Matériel', 'DAM', 'ACME', NULL, 'SAM',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('DRH', 'Direction des Ressources Humaines', 'Direction des Ressources Humaines', 'ACME', 'ACME', NULL, 'DRH',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('PAI', 'Paie', 'Paie', 'DRH', 'ACME', NULL, 'PAI',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('DESR', 'Direction des Études, de la Stratégie et des Risques', 'Direction des Études, de la Stratégie et des Risques', 'ACME', 'ACME', NULL, 'DESR',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('ES', 'Études et Statistiques', 'Études et Statistiques', 'DESR', 'ACME', NULL, 'ES',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('DG', 'Direction Générale', 'Direction Générale', 'ACME', 'ACME', NULL, 'DG',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('ARC', 'Service des Archives', 'Service des Archives', 'DG', 'ACME', 'owner', 'ARC',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('CAICG', 'Cellule d''Audit Interne et du Contrôle de Gestion', 'Cellule d''Audit Interne et du Contrôle de Gestion', 'DG', 'ACME', NULL, 'CAICG',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('CC', 'Comptabilité client', 'Comptabilité client', 'DAF', 'ACME', NULL, 'CC',true);


INSERT INTO "recordsManagement"."descriptionField" ("name", "label", "type", "default", "enumeration") VALUES ('org', 'Organisation', 'name', '', '["ACME Paris","ACME Dakar","ACME Cotonou"]');
INSERT INTO "recordsManagement"."descriptionField" ("name", "label", "type", "default", "enumeration") VALUES ('fullname', 'Nom complet', 'name', '', '');
INSERT INTO "recordsManagement"."descriptionField" ("name", "label", "type", "default", "enumeration") VALUES ('empid', 'Matricule', 'name', '', '');
INSERT INTO "recordsManagement"."descriptionField" ("name", "label", "type", "default", "enumeration") VALUES ('service', 'Entité/Service', 'name', '', '');
INSERT INTO "recordsManagement"."descriptionField" ("name", "label", "type", "default", "enumeration") VALUES ('customer', 'Client', 'text', '', '');
INSERT INTO "recordsManagement"."descriptionField" ("name", "label", "type", "default", "enumeration") VALUES ('salePerson', 'Vendeur', 'text', '', '');


INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('1', 'COUA', 'Courrier Administratif', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('2', 'PRVN', 'Procès-Verbal de Négociation', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('3', 'PRVIF', 'Procès-verbal à Incidence Financière', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('4', 'ETAR', 'État de Rapprochement', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('5', 'RELCC', 'Relevé de Contrôle de Caisse', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('6', 'CTRF', 'Contrat Fournisseur', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('7', 'DCLTVA', 'Déclaration de TVA', 'originatingDate', 'IMP', NULL, NULL, true, 'file');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('8', 'QUTP', 'Quittance de Paiement', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('9', 'FICIC', 'Fiche d''Imputation Comptable', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('10', 'FACJU', 'Facture Justificative', 'originatingDate', 'COM', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('11', 'FICREC', 'Fiche Récapitulative', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('12', 'DOSC', 'Dossiers Caisse', 'originatingDate', NULL, NULL, NULL, true, 'file');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('13', 'PIECD', 'Pièce de Caisse-Dépense', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('14', 'PIEJ', 'Pièce Justificative', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('15', 'DOSB', 'Dossiers Banque', 'originatingDate', NULL, NULL, NULL, true, 'file');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('16', 'PIEBD', 'Pièce de Banque-Dépense', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('17', 'FICDG', 'Fiche DG', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('18', 'NOTSER', 'Note de service', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('19', 'PM', 'Passation de marché', 'originatingDate', NULL, NULL, NULL, true, 'file');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('20', 'BORT', 'Bordereau de transmission', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('21', 'COUNM', 'Courrier de notification de marché', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('22', 'PRVA', 'Procès-Verbal d''Attribution', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('23', 'PRVOP', 'Proces-Verbal d''Ouverture des Plis', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('24', 'RAPEO', 'Rapport d’Évaluation des Offres', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('26', 'DEMC', 'Demande de Cotation', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('27', 'RAPFOR', 'Rapport de Formation', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('28', 'DOSIP', 'Dossier Individuel du Personnel', 'originatingDate', 'DIP', NULL, NULL, true, 'file');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('29', 'ETAC', 'Etat Civil', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('30', 'CURV', 'Curriculum Vitae', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('31', 'EXTAN', 'Extrait d''Acte de Naissance', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('33', 'CASJU', 'Casier Judiciaire', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('34', 'ATTSU', 'Attestation de succès', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('35', 'CTRTRV', 'Contrat de Travail', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('37', 'ATTT', 'Attestation de Travail', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('39', 'CNSS', 'Caisse Nationale de Sécurité Sociale', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('41', 'CAR', 'Carrière', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('42', 'DECIN', 'Décision de nomination', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('43', 'DECIR', 'Décision de redéploiement', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('47', 'ATTF', 'Attestation de formation', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('53', 'COURRN', 'Courrier Répartition du Résultat Net', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('54', 'COUDS', 'Courrier Domiciliation de Salaire', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('55', 'FICP', 'Fiche de Poste', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('56', 'FICF', 'Fiche de Fonction', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('57', 'DEMA', 'Demandes Administratives', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('58', 'COUAA', 'Courrier Autorisation d''Absence', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('59', 'COUCA', 'Courrier Congés Administratifs', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('60', 'COUDE', 'Courrier Demande d''Emploi', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('61', 'DOSETU', 'Dossier de Synthèse et d’Étude', 'originatingDate', NULL, NULL, NULL, true, 'file');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('62', 'FICRM', 'Fiche de Remontée Mensuelle', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('63', 'RAPT', 'Rapport Trimestriel', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('64', 'RAPMOE', 'Rapport de Mise en Œuvre', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('65', 'RAPA', 'Rapport d''Activité', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('66', 'RAPSE', 'Rapport de Suivi et Évaluation', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('67', 'RAPGES', 'Rapport de Gestion', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('68', 'TDRE', 'Termes de Références des Études', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('69', 'DCRN', 'Décret de Nomination', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('70', 'FICCR', 'Fiche de compte rendu', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('71', 'LETC', 'Lettre circulaire', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('72', 'FICI', 'Fiche d''instruction', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('73', 'RAPAMI', 'Rapport d''Audit et Missions Internes', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('74', 'RAPAE', 'Rapport d''Audit Externe', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('75', 'RAPER', 'Rapport d’Études et Recherches', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('76', 'COUABID', 'Courrier Arrivée BID', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('77', 'SMIROP', 'Fiche de visite SMIROP', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('78', 'VISA', 'Visas obtenus', 'originatingDate', NULL, NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('79', 'FACVEN', 'Facture de vente', 'originatingDate', 'COM', NULL, NULL, true, 'item');


INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('7', '8');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('7', '9');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('7', '10');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('7', '11');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('12', '9');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('12', '13');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('12', '14');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('15', '9');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('15', '16');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('15', '14');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '20');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '21');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '22');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '2');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '23');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '24');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '18');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '26');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '29');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '35');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '39');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '41');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '1');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '55');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '57');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '30');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '31');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '33');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '34');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '37');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '77');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '42');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '43');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '47');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '53');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '54');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '56');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '58');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '59');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '60');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '78');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '17');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '69');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '63');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '64');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '65');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '66');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '67');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '1');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('61', '68');

INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CG', 'COUA');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CG', 'PRVN');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CG', 'PRVIF');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CG', 'ETAR');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CG', 'RELCC');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CG', 'CTRF');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('AC', 'DCLTVA');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('AC', 'DOSC');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('AC', 'DOSB');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('AC', 'FICDG');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('AC', 'NOTSER');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('SAM', 'PM');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('SAM', 'RAPFOR');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DRH', 'DOSIP');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('ES', 'DOSETU');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DG', 'FICDG');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DG', 'DCRN');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DG', 'FICCR');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DG', 'LETC');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DG', 'FICI');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DG', 'NOTSER');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DG', 'COUA');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DG', 'BORT');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CAICG', 'FICDG');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CAICG', 'RAPAMI');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CAICG', 'RAPAE');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CAICG', 'RAPER');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CAICG', 'COUABID');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CC', 'FACVEN');

INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('1', 'org', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('2', 'org', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('3', 'org', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('4', 'org', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('5', 'org', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('6', 'org', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('28', 'fullname', false, 1);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('28', 'empid', false, 2);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('61', 'service', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('69', 'service', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('70', 'service', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('71', 'service', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('72', 'service', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('73', 'service', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('74', 'service', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('75', 'service', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('76', 'service', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('79', 'customer', false, 0);
INSERT INTO "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", "required", "position") VALUES ('79', 'salePerson', false, 0);

INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'ppetit', 'PETIT', 'Patricia', 'Mme', 'Patricia PETIT', 'ppetit', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'aadams', 'ADAMS', 'Amy', 'Mme', 'Amy ADAMS', 'aadams', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'madissa', 'ADISSA', 'Marcelin', 'M.', 'Marcelin ADISSA', 'madissa', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'bbardot', 'BARDOT', 'Brigitte', 'Mme', 'Brigitte BARDOT', 'bbardot', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'ddaull', 'DAULL', 'Denis', 'M. ', 'Denis DAULL', 'ddaull', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'aastier', 'ASTIER', 'Alexandre', 'M.', 'Alexandre ASTIER', 'aastier', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'bbain', 'BAIN', 'Barbara', 'Mme', 'Barbara BAIN', 'bbain', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'nboko', 'BOKO', 'Nathalie', 'Mme', 'Nathalie BOKO', 'nboko', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'ggrand', 'GRAND', 'Georges', 'M.', 'Georges GRAND', 'ggrand', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'cchaplin', 'CHAPLIN', 'Charlie', 'M.', 'Charlie CHAPLIN', 'cchaplin', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'sstar', 'STAR', 'Suzanne', 'Mme', 'Suzanne STAR', 'sstar', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'bblier', 'BLIER', 'Bernard', 'M.', 'Bernard BLIER', 'bblier', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'kmama', 'MAMA', 'Karima', 'Mme', 'Karima MAMA', 'kmama', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'ccharles', 'CHARLES', 'Charlotte', 'Mme', 'Charlotte CHARLES', 'ccharles', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'ttong', 'TONG', 'Tony', 'M.', 'Tony TONG', 'ttong', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'ddur', 'DUR', 'Dominique', 'Mme', 'Dominique DUR', 'ddur', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES ('user', 'ssissoko', 'SISSOKO', 'Sylvain', 'M.', 'Sylvain SISSOKO', 'ssissoko', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);

INSERT INTO "auth"."account" ("accountType", "accountId", "displayName", "accountName", "emailAddress", "password", "salt", "tokenDate", "enabled") VALUES ('service', 'System', 'Système', 'Systeme', 'info@maarch.org', 'phdF9WkJuTKkDuPXoqDZuOjLMAFGC6ZrzrSEEqC9YjJN9CZUNWsAOPn1I+PaDT2+g3S2i2/qgt5/Wo4ra68GTAfXSmzR8+IraIzVgvp4+7cQHvlfg7zofQ==', '5440ff64f62bfb39300fc4d46451f5a2', '2018-06-21 09:12:24.256064', true);

INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ppetit', 'ACME', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('aadams', 'DESR', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('madissa', 'DESR', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('bbardot', 'DESR', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ddaull', 'DAF', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('aastier', 'DAM', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('bbain', 'DRH', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('nboko', 'DRH', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ggrand', 'DRH', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('cchaplin', 'CAICG', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('sstar', 'DG', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('bblier', 'ARC', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('kmama', 'ARC', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ccharles', 'ARC', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ttong', 'CC', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ddur', 'CC', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ssissoko', 'CC', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('superadmin', 'SAM', '', true);

INSERT INTO "organization"."servicePosition" ("serviceAccountId", "orgId") VALUES ('System', 'ARC');

INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'ppetit');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'aadams');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'madissa');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'bbardot');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'aastier');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'bbain');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'nboko');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'ggrand');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'cchaplin');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'sstar');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'ttong');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'ddur');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'ssissoko');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'UTILISATEUR', 'ddaull');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'CORRESPONDANT_ARCHIVES', 'bblier');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'CORRESPONDANT_ARCHIVES', 'kmama');
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES ( 'CORRESPONDANT_ARCHIVES', 'ccharles');

INSERT INTO "auth"."servicePrivilege"("accountId", "serviceURI") VALUES  ('System', '*');


-- End of postProcess
