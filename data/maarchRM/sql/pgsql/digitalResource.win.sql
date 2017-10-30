TRUNCATE "digitalResource"."digitalResource" CASCADE;
TRUNCATE "digitalResource"."address" CASCADE;
TRUNCATE "digitalResource"."clusterRepository" CASCADE;
TRUNCATE "digitalResource"."cluster" CASCADE;
TRUNCATE "digitalResource"."repository" CASCADE;

INSERT INTO "digitalResource"."cluster" ("clusterId",  "clusterName", "clusterDescription") VALUES
    ('archives', 'Digital_resource_cluster_for_archives', 'Digital resource cluster for archives');


INSERT INTO "digitalResource"."repository" ("repositoryId", "repositoryName", "repositoryReference", "repositoryType", "repositoryUri", "enabled") VALUES
    ('archives_1', 'Digital resource repository for archives', 'repository_1', 'fileSystem', 'C:/xampp/htdocs/maarchRM/data/maarchRM/repository/archives_1', true),
    ('archives_2', 'Digital resource repository for archives 2', 'repository_2', 'fileSystem', 'C:/xampp/htdocs/maarchRM/data/maarchRM/repository/archives_2', true);


INSERT INTO "digitalResource"."clusterRepository" ("clusterId", "repositoryId", "readPriority", "writePriority", "deletePriority") VALUES
    ('archives', 'archives_1', 1, 1, 1),
    ('archives', 'archives_2', 2, 1, 2);
