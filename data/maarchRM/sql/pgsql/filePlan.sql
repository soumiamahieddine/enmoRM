DELETE FROM "filePlan"."folder";
DELETE FROM "filePlan"."position";

-- communication --
INSERT INTO "filePlan"."folder"
("folderId", "name", "parentFolderId", "description", "ownerOrgRegNumber") VALUES 
('1','Facture client','','Facture client','regNum_CPTCLI'),
('2','Maarch les bains','1','Ville de Maarch les bains','regNum_CPTCLI'),
('3','ACME','1','Societe ACME','regNum_CPTCLI');