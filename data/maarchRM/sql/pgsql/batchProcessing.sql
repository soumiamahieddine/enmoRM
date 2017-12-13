DELETE FROM "batchProcessing"."scheduling";

--scheduling
INSERT INTO "batchProcessing"."scheduling"
("schedulingId", "name", "executedBy", "taskId", "frequency","parameters","lastExecution","nextExecution","status") VALUES
('chainJournalAudit', 'Chaînage audit', 'System', '01', '00;00;;;;;;;',null,null,null,'paused'),
('chainJournalLifeCycle', 'Chaînage du journal du cycle de vie', 'System', '02', '00;01;;;;;;;', null,null,null,'paused'),
('integrity', 'Intégrité', 'System','03', '00;02;;;;;;;',null,null,null,'paused'),
('deleteArchive', 'Destruction', 'System', '04', '00;03;;;;;;;', null,null,null,'paused'),
('sendNotification', 'Envoie des notifications', 'System', '05', '00;04;;;;;;;', null,null,null,'paused');