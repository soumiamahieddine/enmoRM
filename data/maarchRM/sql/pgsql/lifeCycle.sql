TRUNCATE TABLE "lifeCycle"."eventFormat" CASCADE;

-- FR --
INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES

    ('recordsManagement/accessRuleModification', 'resId hashAlgorithm hash address accessRuleStartDate accessRuleDuration previousAccessRuleStartDate previousAccessRuleDuration originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification de la règle de communicabilité de l''archive %6$s'),
    ('recordsManagement/addRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relation ajoutée avec l''archive %6$s'),
    ('recordsManagement/updateRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relation mise à jour avec l''archive %6$s'),
    ('recordsManagement/archivalProfileModification', 'archivalProfileReference', FALSE, 'Modification du profil %6$s.'),
    ('recordsManagement/consultation', 'resId hash hashAlgorith address size', FALSE, 'Consultation de la ressource %9$s'),
    ('recordsManagement/conversion', 'resId hashAlgorithm hash address convertedResId convertedHashAlgorithm convertedHash convertedAddress software docId size', FALSE, 'Conversion du document %18$s'),
    ('recordsManagement/deleteRelationship', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId', FALSE, 'Relation avec l''archive %6$s supprimée'),
    ('recordsManagement/delivery', 'resId hashAlgorithm hash address requesterOrgRegNumber archiverOrgRegNumber size', FALSE, 'Communication de l''archive %6$s'),
    ('recordsManagement/deposit', 'resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber format size', FALSE, 'Dépôt de l''archive %6$s'),
    ('recordsManagement/descriptionModification','property', FALSE, 'Modification des métadonnées de l''archive %6$s.'),
    ('recordsManagement/destructionRequest', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Demande de destruction de l''archive %6$s'),
    ('recordsManagement/destructionRequestCanceling', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Annulation de la demande de destruction de l''archive %6$s'),
    ('recordsManagement/destruction', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Destruction de l''archive %6$s'),
    ('recordsManagement/elimination', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Élimination de l''archive %6$s'),
    ('recordsManagement/freeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Gel de l''archive %6$s'),
    ('recordsManagement/integrityCheck', 'resId hash hashAlgorithm address requesterOrgRegNumber info', FALSE, 'Validation d''intégrité'),
    ('recordsManagement/metadataModification', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification des métadonnées de l''archive %6$s'),
    ('recordsManagement/profileCreation', 'archivalProfileReference', FALSE, 'Création du profil %6$s'),
    ('recordsManagement/profileDestruction', 'archivalProfileReference', FALSE, 'Destruction du profil %6$s'),
    ('recordsManagement/periodicIntegrityCheck', 'startDatetime endDatetime nbArchivesToCheck nbArchivesInSample archivesChecked', FALSE, 'Validation périodique de l''intégrité'),
    ('recordsManagement/restitution', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size', FALSE, 'Restitution de l''archive %6$s'),
    ('recordsManagement/retentionRuleModification', 'resId hashAlgorithm hash address retentionStartDate retentionDuration finalDisposition previousStartDate previousDuration previousFinalDisposition originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Modification de la règle de conservation de l''archive %6$s'),
    ('recordsManagement/unfreeze', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Dégel de l''archive %6$s'),
    ('recordsManagement/resourceDestruction', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Destruction de la ressource %9$s');
   
-- EN --
--INSERT INTO "lifeCycle"."eventFormat" ("type", "format", "notification", "message") VALUES

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
    --('recordsManagement/conversion', 'resId hashAlgorithm hash address convertedResId convertedHashAlgorithm convertedHash convertedAddress software docId', FALSE, 'Conversion of document %17$s'),
    --('recordsManagement/resourceDestruction', 'resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber', FALSE, 'Destruction of resource %9$s');
