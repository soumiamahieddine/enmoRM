TRUNCATE TABLE "lifeCycle"."eventFormat" CASCADE;

-- FR --
INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
    ('recordsManagement/reception', 'hashAlgorithm hash depositorOrgRegNumber', FALSE, 'Réception de l''archive %6$s'),
    ('recordsManagement/validation', 'hashAlgorithm hash', FALSE, 'Validation de l''archive %6$s'),
    ('recordsManagement/deposit', 'resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber format', FALSE, 'Dépôt de l''archive %6$s'),
    ('recordsManagement/depositOfLinkedResource', 'resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber format linkedResId relationshipType', FALSE, 'Ajout de la resource liée %9$s'),
    ('recordsManagement/integrityLifeCycle', 'resId hashAlgorithm hash address requesterOrgRegNumber', FALSE, 'Validation de l''intégrité de l''archive %6$s par le journal de cycle de vie.'),
    ('recordsManagement/integrityDataSystem', 'resId hashAlgorithm hash address requesterOrgRegNumber', FALSE, 'Validation de l''intégrité de l''archive %6$s par le systeme de données.'),
    ('recordsManagement/deleteRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relation avec l''archive %6$s supprimée'),
    ('recordsManagement/addRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relation ajoutée avec l''archive %6$s'),
    ('recordsManagement/retentionRuleModification', 'resId hashAlgorithm hash address retentionStartDate retentionDuration finalDisposition previousStartDate previousDuration previousFinalDisposition originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification de la règle de conservation de l''archive %6$s'),
    ('recordsManagement/accessRuleModification', 'resId hashAlgorithm hash address accessRuleStartDate accessRuleDuration previousAccessRuleStartDate previousAccessRuleDuration originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification de la règle d''accès de l''archive %6$s'),
    ('recordsManagement/freeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Gel de l''archive %6$s'),
    ('recordsManagement/unfreeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Dégel de l''archive %6$s'),
    ('recordsManagement/delivery', 'resId hashAlgorithm hash address requesterOrgRegNumber archiverOrgRegNumber', FALSE, 'Communication de l''archive %6$s'),
    ('recordsManagement/restitution', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Restitution de l''archive %6$s'),
    ('recordsManagement/destruction', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Destruction de l''archive %6$s'),
    ('recordsManagement/profileCreation', 'archivalProfileReference', FALSE, 'Création du profil %6$s'),
    ('recordsManagement/ArchivalProfileModification', 'archivalProfileReference', FALSE, 'Modification du profil %6$s.'),
    ('recordsManagement/profileDestruction', 'archivalProfileReference', FALSE, 'Destruction du profil %6$s'),
    ('recordsManagement/integrityCheck', 'startEventDate endEventDate endEventId', FALSE, 'Validation périodique de l''intégrité'),
    ('recordsManagement/conversion', 'resId hashAlgorithm hash address convertedResId convertedHashAlgorithm convertedHash convertedAddress software docId', FALSE, 'Conversion du document %18$s'),
    ('recordsManagement/descriptionModification','property', FALSE, 'Modification des méta-données de l''archive %6$s.'),
    ('recordsManagement/metadata', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification des métadonnnées de l''archive %6$s'),
    ('recordsManagement/consultation', 'resId hash hashAlgorith address size', FALSE, 'Consultation de la resource %9$s');


-- EN --
--INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES
    --('recordsManagement/reception', 'hashAlgorithm hash depositorOrgRegNumber', FALSE, 'Reception of archive %5$s'),
    --('recordsManagement/validation', 'hashAlgorithm hash', FALSE, 'Validation of archive %5$s'),
    --('recordsManagement/deposit', 'resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber', FALSE, 'Deposit of archive %5$s''),
    --('recordsManagement/integrityLifeCycle', 'resId hashAlgorithm hash address requesterOrgRegNumber', FALSE, 'Validate integrity of archive %65$s' by the life cycle journal.'),
    --('recordsManagement/integrityDataSystem', 'resId hashAlgorithm hash address requesterOrgRegNumber', FALSE, 'Validate integrity of archive %5$s by data system.'),
    --('recordsManagement/deleteRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relationship with the archive %15$s deleted'),
    --('recordsManagement/addRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relationship added with the archive %15$s'),
    --('recordsManagement/retentionRuleModification', 'resId hashAlgorithm hash address retentionStartDate retentionDuration finalDisposition previousStartDate previousDuration previousFinalDisposition originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification of retention rule of the archive %5$s'),
    --('recordsManagement/accessRuleModification', 'resId hashAlgorithm hash address accessRuleStartDate accessRuleDuration previousAccessRuleStartDate previousAccessRuleDuration originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification of access rule of the archive %5$s'),
    --('recordsManagement/freeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Frezze of the archive %5$s'),
    --('recordsManagement/unfreeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Unfrezze of the archive %5$s'),
    --('recordsManagement/delivery', 'resId hashAlgorithm hash address requesterOrgRegNumber archiverOrgRegNumber', FALSE, 'Delivery of archive %5$s'),
    --('recordsManagement/restitution', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Restitution of archive %5$s'),
    --('recordsManagement/destruction', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Destruction of archive %5$s'),
    --('recordsManagement/profileCreation', 'archivalProfileReference', FALSE, 'Creation of profile %5$s'),
    --('recordsManagement/ArchivalProfileModification', 'archivalProfileReference', FALSE, 'Modification of profile %5$s'),
    --('recordsManagement/profileDestruction', 'archivalProfileReference', FALSE, 'Destruction of profile %5$s'),
    --('recordsManagement/integrityCheck', 'startEventDate endEventDate endEventId', FALSE, 'Periodic validation of integrity'),
    --('recordsManagement/conversion', 'resId hashAlgorithm hash address convertedResId convertedHashAlgorithm convertedHash convertedAddress software docId', FALSE, 'Conversion of document %17$s');

