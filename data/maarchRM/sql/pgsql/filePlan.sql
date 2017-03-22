DELETE FROM "filePlan"."folder";
DELETE FROM "filePlan"."position";

-- communication --
INSERT INTO "filePlan"."folder"
("folderId", "name", "parentFolderId", "description", "ownerOrgRegNumber") VALUES 
('1','Adobe','','Facture client d''Adobe','regNum_CPTCLI'),
('2','Eurotunnel','','Facture client d''Eurotunnel','regNum_CPTCLI'),
('3','Eurosam','','Facture client d''Eurosam','regNum_CPTCLI');
