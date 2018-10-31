DELETE FROM "auth"."roleMember";
DELETE FROM "auth"."privilege";
DELETE FROM "auth"."servicePrivilege";
DELETE FROM "auth"."role";
DELETE FROM "auth"."account";

INSERT INTO "auth"."account" ("accountType", "accountId", "lastName", "firstName", "title", "displayName", "accountName", "emailAddress", "password","enabled","passwordChangeRequired","passwordLastChange", "locked", "badPasswordCount","lastLogin","lastIp","replacingUserAccountId") VALUES
    ('user', 'superadmin', 'Super', 'Admin', 'M.', 'super admin', 'superadmin', 'info@maarch.org', '186cf774c97b60a1c106ef718d10970a6a06e06bef89553d9ae65d938a886eae',true,false,null,false,0,null,null,null),
    ('user', 'bblier', 'BLIER', 'Bernard', 'M.', 'Bernard BLIER', 'bblier', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'aastier', 'ASTIER', 'Alexandre', 'M.', 'Alexandre ASTIER', 'aastier', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'rreynolds', 'REYNOLDS', 'Ryan', 'M.', 'Ryan REYNOLDS', 'rreynolds', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'aadams', 'ADAMS', 'Amy', 'Mme.', 'Amy ADAMS', 'aadams', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ppreboist', 'PREBOIST', 'Paul', 'M.', 'Paul PREBOIST', 'ppreboist', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ccox', 'COX', 'Courtney', 'Mme.', 'Courtney COX', 'ccox', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'sstone', 'STONE', 'Sharon', 'Mme.', 'Sharon STONE', 'sstone', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'cchaplin', 'CHAPLIN', 'Charlie', 'M.', 'Charlie CHAPLIN', 'cchaplin', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'sstallone', 'STALLONE', 'Sylvester', 'M.', 'Sylvester STALLONE', 'sstallone', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'rrenaud', 'RENAUD', 'Robert', 'M.', 'M.', 'Renaud RENAUD', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ccordy', 'CORDY', 'Chloé', 'Mme.', 'Chloé CORDY', 'ccordy', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ssissoko', 'SISSOKO', 'Sylvain', 'M.', 'Sylvain SISSOKO', 'ssissoko', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'nnataliy', 'NATALY', 'Nancy', 'Mme.', 'Nancy NATALY', 'nnataliy', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ddur', 'DUR', 'Dominique', 'M.', 'Dominique DUR', 'ddur', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'jjane', 'JANE', 'Jenny', 'Mme.', 'Jenny JANE', 'jjane', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'eerina', 'ERINA', 'Edith', 'Mme.', 'Edith ERINA', 'eerina', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'kkaar', 'KAAR', 'Katy', 'Mme.', 'Katy KAAR', 'kkaar', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ppetit', 'PETIT', 'Patricia', 'Mme.', 'Patricia PETIT', 'ppetit', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'aackermann', 'ACKERMANN', 'Amanda', 'Mme.', 'Amanda ACKERMANN', 'aackermann', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ppruvost', 'PRUVOST', 'Pierre', 'M.', 'Pierre PRUVOST', 'ppruvost', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ttong', 'TONG', 'Tony', 'M.', 'Tony TONG', 'ttong', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'sstar', 'STAR', 'Suzanne', 'Mme.', 'Suzanne STAR', 'sstar', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ssaporta', 'SAPORTA', 'Sabrina', 'Mme.', 'Sabrina SAPORTA', 'ssaporta', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ccharles', 'CHARLES', 'Charlotte', 'Mme.', 'Charlotte CHARLES', 'ccharles', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'mmanfred', 'MANFRED', 'Martin', 'M.', 'Martin MANFRED', 'mmanfred', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'jjonasz', 'JONASZ', 'Jean', 'M.', 'Jean JONASZ', 'jjonasz', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ggrand', 'GRAND', 'George', 'M.', 'George GRAND', 'ggrand', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ccamus', 'CAMUS', 'Cyril', 'M.', 'Cyril CAMUS', 'ccamus', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'hhier', 'HIER', 'Hubert', 'M.', 'Hubert HIER', 'hhier', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ttule', 'TULE', 'Thierry', 'M.', 'Thierry TULE', 'ttule', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'kkrach', 'KRACH', 'Kevin', 'M.', 'Kevin KRACH', 'kkrach', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ddenis', 'DENIS', 'Didier', 'M.', 'Didier DENIS', 'ddenis', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'aalambic', 'ALAMBIC', 'Alain', 'M.', 'Alain ALAMBIC', 'aalambic', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ppacioli', 'PACIOLI', 'Paolo', 'M.', 'Paolo PACIOLI', 'ppacioli', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'vvictoire', 'VICTOIRE', 'Victor', 'M.', 'Victor VICTOIRE', 'vvictoire', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'ddaull', 'DAULL', 'Denis', 'M.', 'Denis DAULL', 'ddaull', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'bboule', 'BOULE', 'Bruno', 'M.', 'Bruno BOULE', 'bboule', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null),
    ('user', 'bbain', 'BAIN', 'Barbara', 'Mme.', 'Barbara BAIN', 'bbain', 'info@maarch.org', 'fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d',true,false,null,false,0,null,null,null);

INSERT INTO "auth"."account" ("accountType", "accountId", "displayName", "accountName", "emailAddress", "enabled") VALUES
    ('service', 'System', 'Système', 'Systeme', 'info@maarch.org', true),
    ('service', 'SystemDepositor', 'Système versant', 'Systeme versant', 'info@maarch.org', true);

-- ROLE
INSERT INTO "auth"."role"("roleId", "roleName", "description", "enabled") VALUES
    ('ADMIN', 'Administrateur', 'Groupe administrateur', true),
    ('CORRESPONDANT_ARCHIVES', 'Archiviste', 'Correspondant d''archives', true),
    ('UTILISATEUR', 'Utilisateur', 'Groupe utilisateur', true); 

-- servicePrivilege
INSERT INTO "auth"."servicePrivilege"("accountId", "serviceURI") VALUES
    ('System', 'recordsManagement/archives/deleteDisposablearchives'),
    ('SystemDepositor', 'recordsManagement/archive/createArchiveBatch'),
    ('System', 'audit/event/createChainjournal'),
    ('System', 'lifeCycle/journal/createChainjournal'),
    ('System', 'recordsmanagement/archivecompliance/readperiodic'),
    ('System', 'batchProcessing/scheduling/updateProcess'),
    ('System', 'recordsManagement/archives/updateArchivesretentionrule');


-- roleMember
INSERT INTO "auth"."roleMember"("roleId", "userAccountId") VALUES
    ('ADMIN', 'superadmin'),
    ('CORRESPONDANT_ARCHIVES', 'bblier'),
    ('UTILISATEUR', 'aastier'),
    ('UTILISATEUR', 'sstallone'),
    ('UTILISATEUR', 'rreynolds'),
    ('UTILISATEUR', 'aadams'),
    ('UTILISATEUR', 'ccox'),
    ('UTILISATEUR', 'sstone'),
    ('UTILISATEUR', 'cchaplin');


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

    ('UTILISATEUR', 'archiveRetrieval/*'),
    ('UTILISATEUR', 'archiveDeposit/*'),
    ('UTILISATEUR', 'archiveManagement/modify'),
    ('UTILISATEUR', 'archiveManagement/modifyDescription'),
    ('UTILISATEUR', 'adminArchive/filePlan');
