-- AUTH
INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES
('user', 'superadmin', 'Super', 'Admin', 'M.', 'super admin', 'superadmin', 'info@maarch.org', '186cf774c97b60a1c106ef718d10970a6a06e06bef89553d9ae65d938a886eae',true,false,null,false,0,null,null,null);

-- ROLE
INSERT INTO "auth"."role"("roleId", "roleName", "description", "enabled") VALUES
('ADMIN', 'Administrateur', 'Groupe administrateur', true);

-- roleMember
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES
('ADMIN', 'superadmin');


-- privilege
INSERT INTO "auth"."privilege"("roleId", "userStory") VALUES
('ADMIN', '*');


-- publicUserStory
INSERT INTO "auth"."publicUserStory"("userStory") VALUES 
('app/*');
	
-- LIFECYCLE
INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
('recordsManagement/accessRuleModification', 'resId hashAlgorithm hash address accessRuleStartDate accessRuleDuration previousAccessRuleStartDate previousAccessRuleDuration originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification de la règle de communicabilité de l''archive %6$s'),
('recordsManagement/addRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relation ajoutée avec l''archive %6$s'),
('recordsManagement/archivalProfileModification', 'archivalProfileReference', FALSE, 'Modification du profil %6$s.'),
('recordsManagement/consultation', 'resId hash hashAlgorith address size', FALSE, 'Consultation de la resource %9$s'),
('recordsManagement/conversion', 'resId hashAlgorithm hash address convertedResId convertedHashAlgorithm convertedHash convertedAddress software docId size', FALSE, 'Conversion du document %18$s'),
('recordsManagement/deleteRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relation avec l''archive %6$s supprimée'),
('recordsManagement/delivery', 'resId hashAlgorithm hash address requesterOrgRegNumber archiverOrgRegNumber size', FALSE, 'Communication de l''archive %6$s'),
('recordsManagement/deposit', 'resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber format size', FALSE, 'Dépôt de l''archive %6$s'),
('recordsManagement/destructionRequest', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Demande de destruction de l''archive %6$s'),
('recordsManagement/destructionRequestCanceling', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Annulation de la demande de destruction de l''archive %6$s'),
('recordsManagement/destruction', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Destruction de l''archive %6$s'),
('recordsManagement/freeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Gel de l''archive %6$s'),
('recordsManagement/integrityCheck', 'resId hash hashAlgorithm address requesterOrgRegNumber info', FALSE, 'Validation d''intégrité'),
('recordsManagement/metadataModification', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification des métadonnnées de l''archive %6$s'),
('recordsManagement/profileCreation', 'archivalProfileReference', FALSE, 'Création du profil %6$s'),
('recordsManagement/profileDestruction', 'archivalProfileReference', FALSE, 'Destruction du profil %6$s'),
('recordsManagement/periodicIntegrityCheck', 'startDatetime endDatetime nbArchivesToCheck nbArchivesInSample archivesChecked', FALSE, 'Validation périodique de l''intégrité'),
('recordsManagement/restitution', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Restitution de l''archive %6$s'),
('recordsManagement/retentionRuleModification', 'resId hashAlgorithm hash address retentionStartDate retentionDuration finalDisposition previousStartDate previousDuration previousFinalDisposition originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification de la règle de conservation de l''archive %6$s'),
('recordsManagement/unfreeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Dégel de l''archive %6$s');


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
('chainJournalLifeCycle', 'Chaînage du journal du cycle de vie', 'System', '02', '00;01;;;;;;;', null,null,null,'paused'),
('integrity', 'Intégrité', 'System','03', '00;02;;;;;;;',null,null,null,'paused'),
('deleteArchive', 'Destruction', 'System', '04', '00;03;;;;;;;', null,null,null,'paused'),
('sendNotification', 'Envoie des notifications', 'System', '05', '00;04;;;;;;;', null,null,null,'paused');