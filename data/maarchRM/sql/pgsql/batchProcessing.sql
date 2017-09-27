DELETE FROM "batchProcessing"."scheduling";
DELETE FROM "batchProcessing"."task";


-- communication --
INSERT INTO "batchProcessing"."task"
("taskId", "route", "description") VALUES
('01', 'audit/event/createChainjournal', 'Chainer le journal de l''application'),
('02', 'lifeCycle/journal/createChainjournal', 'Chainer le journal du cyle de vie'),
('03', 'recordsManagement/archiveCompliance/readPeriodic', 'Valider l''intégrité des archives'),
('04', 'recordsManagement/archives/deleteDisposablearchives', 'Détruire les archives'),
('05', 'batchProcessing/notification/updateProcess', 'Envoyer notification');
('06', 'recordsManagement/archives/updateIndexfulltext', 'Extraction plein texte');


-- communication --
INSERT INTO "batchProcessing"."scheduling" 
("schedulingId", "name", "executedBy", "taskId", "frequency","parameters","lastExecution","nextExecution","status") VALUES 
('chainJournalAudit', 'Chaînage audit', 'System', '01', '00;00;;;;;;;',null,null,null,'scheduled'),
('chainJournalLifeCycle', 'Chaînage journaux', 'System', '02', '00;01;;;;;;;', null,null,null,'scheduled'),
('integrity', 'Intégrité', 'System','03', '00;02;;;;;;;',null,null,null,'scheduled');