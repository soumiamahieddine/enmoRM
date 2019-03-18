TRUNCATE "digitalResource"."digitalResource" CASCADE;
TRUNCATE "digitalResource"."address" CASCADE;
TRUNCATE "digitalResource"."clusterRepository" CASCADE;
TRUNCATE "digitalResource"."cluster" CASCADE;
TRUNCATE "digitalResource"."repository" CASCADE;

INSERT INTO "digitalResource".cluster ("clusterId", "clusterName", "clusterDescription") VALUES ('archives', 'Digital_resource_cluster_for_archives', 'Digital resource cluster for archives');

INSERT INTO "digitalResource"."clusterRepository" ("clusterId", "repositoryId", "writePriority", "readPriority", "deletePriority") VALUES ('archives', 'archives_1', 1, 1, 1);
INSERT INTO "digitalResource"."clusterRepository" ("clusterId", "repositoryId", "writePriority", "readPriority", "deletePriority") VALUES ('archives', 'archives_2', 1, 2, 2);

INSERT INTO "digitalResource"."conversionRule" ("conversionRuleId", puid, "conversionService", "targetPuid") VALUES ('workflow_pod75x-151b-v9jsef', 'fmt/412', 'dependency/fileSystem/plugins/libreOffice', 'fmt/95');
INSERT INTO "digitalResource"."conversionRule" ("conversionRuleId", puid, "conversionService", "targetPuid") VALUES ('workflow_pod763-1691-dli2t0', 'fmt/291', 'dependency/fileSystem/plugins/libreOffice', 'fmt/18');

INSERT INTO "digitalResource".repository ("repositoryId", "repositoryName", "repositoryReference", "repositoryType", "repositoryUri", parameters, "maxSize", enabled) VALUES ('archives_1', 'Digital resource repository for archives', 'repository_1', 'fileSystem', '/var/www/laabs/data/maarchRM/repository/archives_1', NULL, NULL, true);
INSERT INTO "digitalResource".repository ("repositoryId", "repositoryName", "repositoryReference", "repositoryType", "repositoryUri", parameters, "maxSize", enabled) VALUES ('archives_2', 'Digital resource repository for archives 2', 'repository_2', 'fileSystem', '/var/www/laabs/data/maarchRM/repository/archives_2', NULL, NULL, true);
