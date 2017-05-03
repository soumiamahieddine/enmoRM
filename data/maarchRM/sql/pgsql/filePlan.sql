DELETE FROM "filePlan"."folder";
DELETE FROM "filePlan"."position";

-- communication --
INSERT INTO "filePlan"."folder"
("folderId", "name", "parentFolderId", "description", "ownerOrgRegNumber") VALUES 
('1','Adobe',null,'Facture client d''Adobe','regNum_CPTCLI'),
('2','Eurotunnel',null,'Facture client d''Eurotunnel','regNum_CPTCLI'),
('3','Eurosam',null,'Facture client d''Eurosam','regNum_CPTCLI');
