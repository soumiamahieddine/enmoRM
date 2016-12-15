-- schema documentManagement
delete from `documentManagement.documentRelationship`;
delete from `documentManagement.document`;

-- schema recordsManagement
delete from `recordsManagement.archiveRelationship`;  
delete from `recordsManagement.archive`;
delete from `recordsManagement.log`;

-- schema lifeCycle
delete from `lifeCycle.event`;
delete from `lifeCycle.journal`;

-- schema medona
delete from `medona.unitIdentifier`;
delete from `medona.message`;

-- schema digitalResource
delete from `digitalResource.packedResource`;
delete from `digitalResource.package`;
delete from `digitalResource.address`;

delete from `digitalResource.digitalResourceRelationship`;
delete from `digitalResource.digitalResource`;

-- schema audit
delete from `audit.event`;

-- schema businessRecords
delete from `businessRecords.adminDescription`;

-- schema archivesPubliques
delete from `archivesPubliques.keyword`;
delete from `archivesPubliques.custodialHistory`;
delete from `archivesPubliques.contentDescription`;

-- schema busiessRecords
delete from `bankrecords1.customerRecords`;