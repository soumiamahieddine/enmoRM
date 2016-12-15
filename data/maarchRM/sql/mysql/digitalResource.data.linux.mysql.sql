DELETE FROM `digitalResource.address`;
DELETE FROM `digitalResource.digitalResource`;
DELETE FROM `digitalResource.clusterRepository`;
DELETE FROM `digitalResource.cluster`;
DELETE FROM `digitalResource.repository`;

INSERT INTO `digitalResource.cluster` (`clusterId`,  `clusterName`, `clusterDescription`) VALUES
    ('archives', 'Digital_resource_cluster_for_archives', 'Digital resource cluster for archives'),
    ('logs', 'Digital_resource_cluster_for_logs', 'Digital resource cluster for logs');


INSERT INTO `digitalResource.repository` (`repositoryId`, `repositoryName`, `repositoryReference`, `repositoryType`, `repositoryUri`, `enabled`) VALUES
    ('logs_1', 'Digital resource repository for logs', 'repository_1', 'fileSystem', '/var/www/laabs/data/maarchRM/repository/logs_1', true),
    ('logs_2', 'Digital resource repository for logs 2', 'repository_2', 'fileSystem', '/var/www/laabs/data/maarchRM/repository/logs_2', true),
    ('archives_1', 'Digital resource repository for archives', 'fileSystem', '/var/www/laabs/data/maarchRM/repository/archives_1', true),
    ('archives_2', 'Digital resource repository for archives', 'fileSystem', '/var/www/laabs/data/maarchRM/repository/archives_2', true);


INSERT INTO `digitalResource.clusterRepository` (`clusterId`, `repositoryId`, `readPriority`, `writePriority`, `deletePriority`) VALUES
    ('logs', 'logs_1', 1, 1, 1),
    ('archives', 'archives_1', 1, 1, 1),
    ('archives', 'archives_2', 2, 1, 2);
