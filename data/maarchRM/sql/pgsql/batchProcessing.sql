DELETE FROM "batchProcessing"."scheduling";

--scheduling
INSERT INTO "batchProcessing".scheduling ("schedulingId", name, "taskId", frequency, parameters, "executedBy", "lastExecution", "nextExecution", status) VALUES ('chainJournalAudit', 'Chaînage audit', '01', '00;20;;;;;;;', NULL, 'System', '2019-03-14 17:16:46.83441', '2019-03-15 19:00:00', 'scheduled');
INSERT INTO "batchProcessing".scheduling ("schedulingId", name, "taskId", frequency, parameters, "executedBy", "lastExecution", "nextExecution", status) VALUES ('chainJournalLifeCycle', 'Chaînage du journal du cycle de vie', '02', '00;20;;;;;;;', NULL, 'System', '2019-03-14 17:17:08.959422', '2019-03-15 19:00:00', 'scheduled');
INSERT INTO "batchProcessing".scheduling ("schedulingId", name, "taskId", frequency, parameters, "executedBy", "lastExecution", "nextExecution", status) VALUES ('deleteArchive', 'Destruction', '04', '00;19;;;;;;;', NULL, 'System', '2019-03-14 17:17:10.155329', '2019-03-15 18:00:00', 'scheduled');
INSERT INTO "batchProcessing".scheduling ("schedulingId", name, "taskId", frequency, parameters, "executedBy", "lastExecution", "nextExecution", status) VALUES ('integrity', 'Intégrité', '03', '00;01;;;;4;H;00;20', NULL, 'System', '2019-03-14 17:17:41.825506', '2019-03-14 21:17:41.825513', 'scheduled');
