DELETE FROM `digitalResource.digitalResource`;
DELETE FROM `digitalResource.address`;
DELETE FROM `digitalResource.clusterRepository`;
DELETE FROM `digitalResource.cluster`;
DELETE FROM `digitalResource.repository`;

INSERT INTO `digitalResource.cluster` (`clusterId`,  `clusterName`, `clusterDescription`) VALUES
    ('archives', 'Digital_resource_cluster_for_archives', 'Digital resource cluster for archives');


INSERT INTO `digitalResource.repository` (`repositoryId`, `repositoryName`, `repositoryReference`, `repositoryType`, `repositoryUri`, `enabled`) VALUES
    ('archives_1', 'Digital resource repository for archives', 'repository_1', 'fileSystem', 'C:/xampp/htdocs/laabs/data/maarchRM/repository/archives_1', true),
    ('archives_2', 'Digital resource repository for archives', 'repository_2', 'fileSystem', 'C:/xampp/htdocs/laabs/data/maarchRM/repository/archives_2', true);


INSERT INTO `digitalResource.clusterRepository` (`clusterId`, `repositoryId`, `readPriority`, `writePriority`, `deletePriority`) VALUES
    ('archives', 'archives_1', 1, 1, 1),
    ('archives', 'archives_2', 2, 1, 2);
