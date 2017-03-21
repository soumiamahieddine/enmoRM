DELETE FROM "filePlan"."folder";
DELETE FROM "filePlan"."position";

-- communication --
INSERT INTO "filePlan"."folder"
("folderId", "name", "parentFolderId", "description", "ownerOrgRegNumber") VALUES 
('1','Facture client',null,'Facture client','regNum_CPTCLI'),
('2','Thomson-CSF','1','Facture client','regNum_CPTCLI'),
('3','Hummingbird','1','Facture client','regNum_CPTCLI'),
('4','Adobe','1','Facture client','regNum_CPTCLI'),
('5','Maarch les bains','1','Ville de Maarch les bains','regNum_CPTCLI'),
('6','ACME','1','Societe ACME','regNum_CPTCLI');