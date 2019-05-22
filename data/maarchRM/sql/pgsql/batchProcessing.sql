DELETE FROM "batchProcessing"."scheduling";

--scheduling
INSERT INTO "batchProcessing".scheduling 
("schedulingId", name, "taskId", frequency, parameters, "executedBy", "lastExecution", "nextExecution", status) VALUES 
('chainJournalAudit', 'Chaînage audit', '01', '00;20;;;;;;;', NULL, 'System', '2019-03-14 17:16:46.83441', '2019-03-15 19:00:00', 'scheduled'),
('chainJournalLifeCycle', 'Chaînage du journal du cycle de vie', '02', '00;20;;;;;;;', NULL, 'System', '2019-03-14 17:17:08.959422', '2019-03-15 19:00:00', 'scheduled'),
('deleteArchive', 'Destruction', '04', '00;19;;;;;;;', NULL, 'System', '2019-03-14 17:17:10.155329', '2019-03-15 18:00:00', 'scheduled'),
('integrity', 'Intégrité', '03', '00;01;;;;4;H;00;20', NULL, 'System', '2019-03-14 17:17:41.825506', '2019-03-14 21:17:41.825513', 'scheduled');

INSERT INTO "batchProcessing"."scheduling" 
("schedulingId", "name", "executedBy", "taskId", "frequency","parameters","lastExecution","nextExecution","status") VALUES
('processDelivery', 'Traiter les communications', 'System','04', '00;03;;;;;;;',null,null,null,'paused'),
('processdestruction', 'Traiter les destructions', 'System','05', '00;04;;;;;;;',null,null,null,'paused'),
('processRestitution', 'Traiter les restitutions', 'System','06', '00;05;;;;;;;',null,null,null,'paused'),
('processTransfer', 'Traiter les transferts', 'System','07', '00;06;;;;;;;',null,null,null,'paused'),
('validateTransfer', 'Valider les transfert', 'System','08', '00;07;;;;;;;',null,null,null,'paused'),
('purge', 'Purge', 'System', '09', '00;08;;;;;;;',null,null,null,'paused');