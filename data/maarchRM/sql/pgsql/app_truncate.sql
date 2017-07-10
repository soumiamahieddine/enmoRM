-- schema documentManagement
delete from "documentManagement"."documentRelationship";
delete from "documentManagement"."document";

-- schema recordsManagement
delete from "recordsManagement"."archiveRelationship";  
delete from "recordsManagement"."archive";
delete from "recordsManagement"."log";

--schema lifeCycle
delete from "lifeCycle"."event";

-- schema digitalResource
delete from "digitalResource"."packedResource";
delete from "digitalResource"."package";
delete from "digitalResource"."address";

delete from "digitalResource"."digitalResource";

-- schema audit
delete from "audit"."event";

delete from "filePlan"."position";