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

    -- privilege
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
    ('UTILISATEUR', 'archiveManagement/retrieve'),
    ('UTILISATEUR', 'archiveManagement/filePlan');


-- roleMember
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES
('ADMIN', 'superadmin');


-- publicUserStory
INSERT INTO "auth"."publicUserStory"("userStory") VALUES 
('app/*');
    
-- LIFECYCLE
INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
('recordsManagement/accessRuleModification', 'resId hashAlgorithm hash address accessRuleStartDate accessRuleDuration previousAccessRuleStartDate previousAccessRuleDuration originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification de la règle d''accès de l''archive %6$s'),
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
    
-- "organization".orgRole
INSERT INTO "organization"."orgRole" ("code", "name","description") VALUES
('owner','organization/owner', 'The system owner');

INSERT INTO "batchProcessing"."task"
("taskId", "route", "description") VALUES
('01', 'audit/event/createChainjournal', 'Chainer le journal de l''application'),
('02', 'lifeCycle/journal/createChainjournal', 'Chainer le journal du cyle de vie'),
('03', 'recordsManagement/archiveCompliance/readPeriodic', 'Valider l''intégrité des archives'),
('04', 'recordsManagement/archives/deleteDisposablearchives', 'Détruire les archives'),
('05', 'batchProcessing/notification/updateProcess', 'Envoyer notification'),
('06', 'recordsManagement/archives/updateIndexfulltext', 'Extraction plein texte');


INSERT INTO "batchProcessing"."scheduling"
("schedulingId", "name", "executedBy", "taskId", "frequency","parameters","lastExecution","nextExecution","status") VALUES
('chainJournalAudit', 'Chaînage audit', 'System', '01', '00;00;;;;;;;',null,null,null,'paused'),
('chainJournalLifeCycle', 'Chaînage journaux', 'System', '02', '00;01;;;;;;;', null,null,null,'paused'),
('integrity', 'Intégrité', 'System','03', '00;02;;;;;;;',null,null,null,'paused'),
('deleteArchive', 'Destruction', 'System', '04', '00;03;;;;;;;', null,null,null,'paused'),
('sendNotification', 'Envoie des notifications', 'System', '05', '00;04;;;;;;;', null,null,null,'paused');


INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES ('BulletinsDePaie', 'Bulletins de paie', 'Code du Travail, art. L3243-4 - Code de la Sécurité Sociale, art. L243-12', 'P5Y', 'destruction');
INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES ('DossierDuPersonnel', 'Dossier du personnel', 'Convention Collective nationale de retraite et de prévoyance des cadres, art. 23', 'P90Y', 'destruction');
INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES  ('DocumentComptables', 'Documents comptables', 'Code du commerce, Article L123-22', 'P10Y', 'destruction');
INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES ('ControleImpot', 'Contrôle de l''impôt', 'Livre des Procédures Fiscales, art 102 B et L 169 : Livres, registres, documents ou pièces sur lesquels peuvent s''exercer les droits de communication, d''enquête et de contrôle de l''administration', 'P6Y', 'destruction');
INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES ('ImpotSociete', 'Impôt sur les sociétés et liasses fiscales', 'Livre des Procédures Fiscales, art 102 B et L 169 alinea 2: Les registres tenus en application du 9 de l''article 298 sexdecies F du code général des impôts et du 5 de l''article 298 sexdecies G du même code', 'P10Y', 'destruction');
INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES ('ImpotsAutres', 'Taxe professionnelle', 'Livre des Procédures Fiscales, art 102 B et L 169 alinea 3', 'P3Y', 'destruction');
INSERT INTO "recordsManagement"."retentionRule" ("code", "label", "description", "duration", "finalDisposition") VALUES ('DocumentsGestion', 'Documents de gestion', 'Documents de gestion', 'P5Y', 'destruction');

INSERT INTO "digitalResource"."cluster" ("clusterId",  "clusterName", "clusterDescription") VALUES
    ('archives', 'Digital_resource_cluster_for_archives', 'Digital resource cluster for archives');


INSERT INTO "digitalResource"."repository" ("repositoryId", "repositoryName", "repositoryReference", "repositoryType", "repositoryUri", "enabled") VALUES
    ('archives_1', 'Digital resource repository for archives', 'repository_1', 'fileSystem', '/var/www/laabs/data/maarchRM/repository/archives_1', true),
    ('archives_2', 'Digital resource repository for archives 2', 'repository_2', 'fileSystem', '/var/www/laabs/data/maarchRM/repository/archives_2', true);


INSERT INTO "digitalResource"."clusterRepository" ("clusterId", "repositoryId", "readPriority", "writePriority", "deletePriority") VALUES
    ('archives', 'archives_1', 1, 1, 1),
    ('archives', 'archives_2', 2, 1, 2);






INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('ArchivesConservationMemoireElectronique', 'Archives Conservation et Mémoire Électronique', 'Archives Conservation et Mémoire Électronique', NULL, NULL, NULL, 'ArchivesConservationMemoireElectronique',false);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('DirectionAdministrativesFinanciere', 'Direction Administratives et Financière', 'Direction Administratives et Financière', 'ArchivesConservationMemoireElectronique', 'ArchivesConservationMemoireElectronique', NULL, 'DirectionAdministrativesFinanciere',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('ComptabiliteGenerale', 'Comptabilité Générale', 'Comptabilité Générale', 'DirectionAdministrativesFinanciere', 'ArchivesConservationMemoireElectronique', NULL, 'ComptabiliteGenerale',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('AgenceComptable', 'Agence comptable', 'Agence comptable', 'DirectionAdministrativesFinanciere', 'ArchivesConservationMemoireElectronique', NULL, 'AgenceComptable',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('ServiceArchives', 'Service des Archives', 'Service des Archives', 'DirectionGenerale', 'ArchivesConservationMemoireElectronique', 'owner', 'ServiceArchives',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('DirectionRessourcesHumaines', 'Direction des Ressources Humaines', 'Direction des Ressources Humaines', 'ArchivesConservationMemoireElectronique', 'ArchivesConservationMemoireElectronique', NULL, 'DirectionRessourcesHumaines',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('DirectionEtudes', 'Direction des Études, de la Stratégie et des Risques', 'Direction des Études, de la Stratégie et des Risques', 'ArchivesConservationMemoireElectronique', 'ArchivesConservationMemoireElectronique', NULL, 'DirectionEtudes',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('EtudesStatistiques', 'Études et Statistiques', 'Études et Statistiques', 'DirectionEtudes', 'ArchivesConservationMemoireElectronique', NULL, 'EtudesStatistiques',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('DirectionGenerale', 'Direction Générale', 'Direction Générale', 'ArchivesConservationMemoireElectronique', 'ArchivesConservationMemoireElectronique', NULL, 'DirectionGenerale',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('CelluleAuditInterneControleGestion', 'Cellule d''Audit Interne et du Contrôle de Gestion', 'Cellule d''Audit Interne et du Contrôle de Gestion', 'DirectionGenerale', 'ArchivesConservationMemoireElectronique', NULL, 'CelluleAuditInterneControleGestion',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('ComptabiliteClient', 'Comptabilité client', 'Comptabilité client', 'DirectionAdministrativesFinanciere', 'ArchivesConservationMemoireElectronique', NULL, 'ComptabiliteClient',true);
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES ('Paie', 'Paie', 'Paie', 'DirectionRessourcesHumaines', 'ArchivesConservationMemoireElectronique', NULL, 'Paie',true);


INSERT INTO "recordsManagement"."descriptionField" ("name", "label", "type", "default", "enumeration") VALUES ('org', 'Organisation', 'name', '', '["ArchivesConservationMemoireElectronique Paris","ArchivesConservationMemoireElectronique Dakar","ArchivesConservationMemoireElectronique Cotonou"]');
INSERT INTO "recordsManagement"."descriptionField" ("name", "label", "type", "default", "enumeration") VALUES ('fullname', 'Nom complet', 'name', '', '');
INSERT INTO "recordsManagement"."descriptionField" ("name", "label", "type", "default", "enumeration") VALUES ('empid', 'Matricule', 'name', '', '');
INSERT INTO "recordsManagement"."descriptionField" ("name", "label", "type", "default", "enumeration") VALUES ('service', 'Entité/Service', 'name', '', '');
INSERT INTO "recordsManagement"."descriptionField" ("name", "label", "type", "default", "enumeration") VALUES ('customer', 'Client', 'text', '', '');
INSERT INTO "recordsManagement"."descriptionField" ("name", "label", "type", "default", "enumeration") VALUES ('salePerson', 'Vendeur', 'text', '', '');


INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('1', 'COUA', 'Courrier Administratif', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('2', 'PRVN', 'Procès-Verbal de Négociation', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('3', 'PRVIF', 'Procès-verbal à Incidence Financière', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('4', 'ETAR', 'État de Rapprochement', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('5', 'RELCC', 'Relevé de Contrôle de Caisse', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('6', 'CTRF', 'Contrat Fournisseur', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('7', 'DCLTVA', 'Déclaration de TVA', 'depositDate', 'document', NULL, NULL, true, 'file');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('8', 'QUTP', 'Quittance de Paiement', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('9', 'FICIC', 'Fiche d''Imputation Comptable', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('10', 'FACJU', 'Facture Justificative', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('11', 'FICREC', 'Fiche Récapitulative', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('12', 'DOSC', 'Dossiers Caisse', 'depositDate', 'document', NULL, NULL, true, 'file');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('13', 'PIECD', 'Pièce de Caisse-Dépense', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('14', 'PIEJ', 'Pièce Justificative', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('15', 'DOSB', 'Dossiers Banque', 'depositDate', 'document', NULL, NULL, true, 'file');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('16', 'PIEBD', 'Pièce de Banque-Dépense', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('17', 'FICDG', 'Fiche DG', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('18', 'NOTSER', 'Note de service', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('19', 'PM', 'Passation de marché', 'depositDate', 'document', NULL, NULL, true, 'file');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('20', 'BORT', 'Bordereau de transmission', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('21', 'COUNM', 'Courrier de notification de marché', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('22', 'PRVA', 'Procès-Verbal d''Attribution', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('23', 'PRVOP', 'Proces-Verbal d''Ouverture des Plis', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('24', 'RAPEO', 'Rapport d’Évaluation des Offres', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('26', 'DEMC', 'Demande de Cotation', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('27', 'RAPFOR', 'Rapport de Formation', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('28', 'DOSIP', 'Dossier Individuel du Personnel', 'depositDate', 'document', NULL, NULL, true, 'file');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('29', 'ETAC', 'Etat Civil', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('30', 'CURV', 'Curriculum Vitae', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('31', 'EXTAN', 'Extrait d''Acte de Naissance', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('33', 'CASJU', 'Casier Judiciaire', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('34', 'ATTSU', 'Attestation de succès', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('35', 'CTRTRV', 'Contrat de Travail', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('37', 'ATTT', 'Attestation de Travail', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('39', 'CNSS', 'Caisse Nationale de Sécurité Sociale', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('41', 'CAR', 'Carrière', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('42', 'DECIN', 'Décision de nomination', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('43', 'DECIR', 'Décision de redéploiement', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('47', 'ATTF', 'Attestation de formation', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('53', 'COURRN', 'Courrier Répartition du Résultat Net', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('54', 'COUDS', 'Courrier Domiciliation de Salaire', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('55', 'FICP', 'Fiche de Poste', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('56', 'FICF', 'Fiche de Fonction', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('57', 'DEMA', 'Demandes Administratives', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('58', 'COUAA', 'Courrier Autorisation d''Absence', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('59', 'COUCA', 'Courrier Congés Administratifs', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('60', 'COUDE', 'Courrier Demande d''Emploi', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('61', 'DOSETU', 'Dossier de Synthèse et d’Étude', 'depositDate', 'document', NULL, NULL, true, 'file');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('62', 'FICRM', 'Fiche de Remontée Mensuelle', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('63', 'RAPT', 'Rapport Trimestriel', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('64', 'RAPMOE', 'Rapport de Mise en Œuvre', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('65', 'RAPA', 'Rapport d''Activité', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('66', 'RAPSE', 'Rapport de Suivi et Évaluation', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('67', 'RAPGES', 'Rapport de Gestion', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('68', 'TDRE', 'Termes de Références des Études', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('69', 'DCRN', 'Décret de Nomination', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('70', 'FICCR', 'Fiche de compte rendu', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('71', 'LETC', 'Lettre circulaire', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('72', 'FICI', 'Fiche d''instruction', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('73', 'RAPAMI', 'Rapport d''Audit et Missions Internes', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('74', 'RAPAE', 'Rapport d''Audit Externe', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('75', 'RAPER', 'Rapport d’Études et Recherches', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('76', 'COUABID', 'Courrier Arrivée BID', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('77', 'SMIROP', 'Fiche de visite SMIROP', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('78', 'VISA', 'Visas obtenus', 'depositDate', 'document', NULL, NULL, true, 'item');
INSERT INTO "recordsManagement"."archivalProfile" ("archivalProfileId", "reference", "name", "retentionStartDate", "retentionRuleCode", "description", "accessRuleCode", "acceptUserIndex", "fileplanLevel") VALUES ('79', 'FACVEN', 'Facture de vente', 'depositDate', 'document', NULL, NULL, true, 'item');


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
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('19', '25');
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
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '32');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '33');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '34');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '36');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '37');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '38');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '40');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '77');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '42');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '43');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '25');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '44');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '45');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '46');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '47');

INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '48');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '49');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '50');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '51');
INSERT INTO "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") VALUES ('28', '52');
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

INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('ComptabiliteGenerale', 'COUA');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('ComptabiliteGenerale', 'PRVN');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('ComptabiliteGenerale', 'PRVIF');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('ComptabiliteGenerale', 'ETAR');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('ComptabiliteGenerale', 'RELCC');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('ComptabiliteGenerale', 'CTRF');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('AgenceComptable', 'DCLTVA');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('AgenceComptable', 'DOSC');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('AgenceComptable', 'DOSB');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('AgenceComptable', 'FICDG');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('AgenceComptable', 'NOTSER');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DirectionRessourcesHumaines', 'DOSIP');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('EtudesStatistiques', 'DOSETU');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DirectionGenerale', 'FICDG');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DirectionGenerale', 'DCRN');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DirectionGenerale', 'FICCR');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DirectionGenerale', 'LETC');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DirectionGenerale', 'FICI');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DirectionGenerale', 'NOTSER');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DirectionGenerale', 'COUA');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('DirectionGenerale', 'BORT');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CelluleAuditInterneControleGestion', 'FICDG');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CelluleAuditInterneControleGestion', 'RAPAMI');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CelluleAuditInterneControleGestion', 'RAPAE');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CelluleAuditInterneControleGestion', 'RAPER');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('CelluleAuditInterneControleGestion', 'COUABID');
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference") VALUES ('ComptabiliteClient', 'FACVEN');

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


INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ppetit', 'ArchivesConservationMemoireElectronique', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('aadams', 'DirectionEtudes', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('madissa', 'DirectionEtudes', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('bbardot', 'DirectionEtudes', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ddaull', 'DirectionAdministrativesFinanciere', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('bbain', 'DirectionRessourcesHumaines', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('nboko', 'DirectionRessourcesHumaines', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ggrand', 'DirectionRessourcesHumaines', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('cchaplin', 'CelluleAuditInterneControleGestion', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('sstar', 'DirectionGenerale', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('bblier', 'ServiceArchives', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('kmama', 'ServiceArchives', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ccharles', 'ServiceArchives', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ttong', 'ComptabiliteClient', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ddur', 'ComptabiliteClient', '', true);
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES ('ssissoko', 'ComptabiliteClient', '', true);


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
-- Insert postprocess SQL queries here
INSERT INTO "auth"."account" ("accountType", "accountId", "displayName", "accountName", "emailAddress", "enabled") VALUES ('service', 'System', 'Système', 'Systeme', 'info@maarch.org', true); 

INSERT INTO "auth"."servicePrivilege"("accountId", "serviceURI") VALUES  ('System', '*');


-- End of postProcess
