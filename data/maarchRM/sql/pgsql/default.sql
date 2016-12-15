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
('recordsManagement/reception', 'hashAlgorithm hash depositorOrgRegNumber', FALSE, 'Réception de l''archive %6$s'),
('recordsManagement/validation', 'hashAlgorithm hash', FALSE, 'Validation de l''archive %6$s'),
('recordsManagement/deposit', 'resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber', FALSE, 'Dépôt de l''archive %6$s'),
('recordsManagement/integrityLifeCycle', 'resId hashAlgorithm hash address requesterOrgRegNumber', FALSE, 'Validation de l''intégrité de l''archive %6$s par le journal de cycle de vie.'),
('recordsManagement/integrityDataSystem', 'resId hashAlgorithm hash address requesterOrgRegNumber', FALSE, 'Validation de l''intégrité de l''archive %6$s par le systeme de données.'),
('recordsManagement/deleteRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relation avec l''archive %6$s supprimée'),
('recordsManagement/addRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relation ajoutée avec l''archive %6$s'),
('recordsManagement/retentionRuleModification', 'resId hashAlgorithm hash address retentionStartDate retentionDuration finalDisposition previousStartDate previousDuration previousFinalDisposition originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification de la règle de retention de l''archive %6$s'),
('recordsManagement/accessRuleModification', 'resId hashAlgorithm hash address accessRuleStartDate accessRuleDuration previousAccessRuleStartDate previousAccessRuleDuration originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification de la règle d''accès de l''archive %6$s'),
('recordsManagement/freeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Gel de l''archive %6$s'),
('recordsManagement/unfreeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Dégel de l''archive %6$s'),
('recordsManagement/delivery', 'resId hashAlgorithm hash address requesterOrgRegNumber archiverOrgRegNumber', FALSE, 'Communication de l''archive %6$s'),
('recordsManagement/restitution', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Restitution de l''archive %6$s'),
('recordsManagement/destruction', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Destruction de l''archive %6$s'),
('recordsManagement/profileCreation', 'archivalProfileReference', FALSE, 'Création du profil %6$s'),
('recordsManagement/ArchivalProfileModification', 'archivalProfileReference', FALSE, 'Modification du profil %6$s.'),
('recordsManagement/profileDestruction', 'archivalProfileReference', FALSE, 'Destruction du profil %6$s'),
('recordsManagement/integrityCheck', 'startEventDate endEventDate endEventId', FALSE, 'Validation périodique de l''intégrité'),
('recordsManagement/conversion', 'resId hashAlgorithm hash address convertedResId convertedHashAlgorithm convertedHash convertedAddress software docId', FALSE, 'Conversion du document %18$s');

-- ORGANIZATION
-- "organization".orgRole
INSERT INTO "organization"."orgRole" ("code", "name","description") VALUES
('owner','organization/owner', 'The system owner');
