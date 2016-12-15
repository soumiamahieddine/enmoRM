DELETE FROM `batchProcessing.scheduling`;
DELETE FROM `batchProcessing.task`;


-- communication --
INSERT INTO `batchProcessing.task` 
(`taskId`, `route`, `description`) VALUES 
('01', 'audit/event/createChainjournal', 'Chainer le journal de l''application'),
('02', 'lifeCycle/journal/createChainjournal', 'Chainer le journal du cyle de vie'),
('03', 'medona/ArchiveDelivery/updateProcessBatch', 'Traitement des demandes de communication'),
('04', 'medona/ArchiveDestruction/updateProcessAll', 'Traitement des demandes de destruction'),
('05', 'medona/ArchiveRestitution/updateProcessBatch', 'Traitement des demandes de restitution'),
('06', 'medona/archiveRestitution/updateValidateBatch', 'Validation des demandes de restitution'),
('07', 'recordsmanagement/archivecompliance/readperiodic', 'Intégrité périodique'),
('08', 'medona/ArchiveTransfer/updateProcessBatch', 'Traitement des transferts d''archives'),
('09', 'medona/ArchiveTransfer/updateValidateBatch', 'Validation des transferts d''archives'),
('10', 'medona/documentConversion/readProcessAll', 'Traitement des demandes de conversion');

-- scheduling --
INSERT INTO `batchProcessing.scheduling` (`schedulingId`, `taskId`, `frequency`) VALUES
    ('1','01','0 0 MON,TUE,WED,THU,FRI,SAT,SUN 1 S'),
    ('2','02','0 0 MON,TUE,WED,THU,FRI,SAT,SUN 1 S');
