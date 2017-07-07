DELETE FROM "organization"."archivalProfileAccess";
DELETE FROM "organization"."userPosition";
DELETE FROM "organization"."servicePosition";
DELETE FROM "organization"."orgContact";
DELETE FROM "organization"."organization";
DELETE FROM "organization"."orgType";
DELETE FROM "organization"."orgRole";

-- Organization unit types
INSERT INTO "organization"."orgType"("code", "name") VALUES 
('Collectivite', 'Collectivité'),
('Societe', 'Société'),
('Direction', 'Direction d''une entreprise ou d''une collectivité'),
('Service', 'Service d''une entreprise ou d''une collectivité'),
('Division', 'Division d''une entreprise');

-- "organization".orgRole
INSERT INTO "organization"."orgRole" ("code", "name","description") VALUES
('owner','organization/owner', 'The system owner');

-- Organizations
INSERT INTO "organization"."organization"( "orgId", "orgName", "displayName", "parentOrgId", "ownerOrgId", "orgRoleCodes", "registrationNumber", "isOrgUnit") VALUES 
('ACME', 'ACME','Archivage Conversation et Mémoire Electronique ', NULL, NULL, NULL, 'regNum_ACME', false),
    ('DSI', 'DSI','Direction des Systèmes d’Information', 'ACME', 'ACME', NULL, 'regNum_DSI', true),
        ('ADMINSYS', 'Admin Sys','Administration Système', 'DSI', 'ACME', 'owner', 'regNum_ADMINSYS', true),
    ('DAF', 'DAF','Direction Administrative et Financière', 'ACME', 'ACME', NULL, 'regNum_DAF', true),
        ('CPT', 'CPT','Compta/Finance', 'DAF', 'ACME', NULL, 'regNum_CPT', true),
            ('CPTCLI', 'CPTCLI','Service compta clients', 'CPT', 'ACME', NULL, 'regNum_CPTCLI', true),
            ('CPTFOUR', 'CPTFOUR','Service compta fournisseurs', 'CPT', 'ACME', NULL, 'regNum_CPTFOUR', true),
        ('RH', 'RH','Ressource humaine', 'DAF', 'ACME', NULL, 'regNum_RH', true);

-- Organization user position
INSERT INTO "organization"."userPosition" ("userAccountId", "orgId", "function", "default") VALUES
('sstallone', 'DSI', '', true),
('bblier', 'ADMINSYS', '', true),
('rreynolds', 'CPT', '', true),
('aadams', 'CPTCLI', '', true),
('ccox', 'CPTFOUR', '', true),
('sstone', 'RH', '', true);

-- Organization service position
INSERT INTO "organization"."servicePosition" ("serviceAccountId", "orgId") VALUES
('System', 'CPT');

-- Organization service position
INSERT INTO "organization"."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess") VALUES
('CPTCLI', 'FacturesClients', true),
('CPTFOUR', 'FacturesFournisseurs', true);
