--
-- PostgreSQL database dump
--

-- Dumped from database version 9.5.12
-- Dumped by pg_dump version 9.5.12

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET client_min_messages = warning;
SET row_security = off;

ALTER TABLE ONLY "recordsManagement".archive DROP CONSTRAINT "archive_parentArchiveId_fkey";
ALTER TABLE ONLY "recordsManagement".archive DROP CONSTRAINT "archive_accessRuleCode_fkey";
ALTER TABLE ONLY "recordsManagement"."archiveRelationship" DROP CONSTRAINT "archiveRelationship_relatedArchiveId_fkey";
ALTER TABLE ONLY "recordsManagement"."archiveRelationship" DROP CONSTRAINT "archiveRelationship_archiveId_fkey";
ALTER TABLE ONLY "recordsManagement"."archiveDescription" DROP CONSTRAINT "archiveDescription_archivalProfileId_fkey";
ALTER TABLE ONLY "recordsManagement"."archivalProfile" DROP CONSTRAINT "archivalProfile_retentionRuleCode_fkey";
ALTER TABLE ONLY "recordsManagement"."archivalProfile" DROP CONSTRAINT "archivalProfile_accessRuleCode_fkey";
ALTER TABLE ONLY "recordsManagement"."archivalProfileContents" DROP CONSTRAINT "archivalProfileContents_parentProfileId_fkey";
ALTER TABLE ONLY "recordsManagement"."archivalProfileContents" DROP CONSTRAINT "archivalProfileContents_containedProfileId_fkey";
ALTER TABLE ONLY organization."userPosition" DROP CONSTRAINT "userPosition_orgId_fkey";
ALTER TABLE ONLY organization.organization DROP CONSTRAINT "organization_parentOrgId_fkey";
ALTER TABLE ONLY organization.organization DROP CONSTRAINT "organization_ownerOrgId_fkey";
ALTER TABLE ONLY organization.organization DROP CONSTRAINT "organization_orgTypeCode_fkey";
ALTER TABLE ONLY organization."orgContact" DROP CONSTRAINT "orgContact_orgId_fkey";
ALTER TABLE ONLY organization."archivalProfileAccess" DROP CONSTRAINT "archivalProfileAccess_orgId_fkey";
ALTER TABLE ONLY medona."unitIdentifier" DROP CONSTRAINT "unitIdentifier_messageId_fkey";
ALTER TABLE ONLY medona."messageComment" DROP CONSTRAINT "messageComment_messageId_fkey";
ALTER TABLE ONLY "filePlan"."position" DROP CONSTRAINT "position_filePlan_fkey";
ALTER TABLE ONLY "filePlan".folder DROP CONSTRAINT "folderId_filePlan_fkey";
ALTER TABLE ONLY "digitalResource"."packedResource" DROP CONSTRAINT "packedResource_resId_fkey";
ALTER TABLE ONLY "digitalResource"."packedResource" DROP CONSTRAINT "packedResource_packageId_fkey";
ALTER TABLE ONLY "digitalResource".package DROP CONSTRAINT "package_packageId_fkey";
ALTER TABLE ONLY "digitalResource"."digitalResource" DROP CONSTRAINT "digitalResource_relatedResId_fkey";
ALTER TABLE ONLY "digitalResource"."digitalResource" DROP CONSTRAINT "digitalResource_clusterId_fkey";
ALTER TABLE ONLY "digitalResource"."digitalResource" DROP CONSTRAINT "digitalResource_archiveId_fkey";
ALTER TABLE ONLY "digitalResource"."clusterRepository" DROP CONSTRAINT "clusterRepository_repositoryId_fkey";
ALTER TABLE ONLY "digitalResource"."clusterRepository" DROP CONSTRAINT "clusterRepository_clusterId_fkey";
ALTER TABLE ONLY "digitalResource".address DROP CONSTRAINT "address_resId_fkey";
ALTER TABLE ONLY "digitalResource".address DROP CONSTRAINT "address_repositoryId_fkey";
ALTER TABLE ONLY contact.communication DROP CONSTRAINT "communication_contactId_fkey";
ALTER TABLE ONLY contact.communication DROP CONSTRAINT "communication_comMeanCode_fkey";
ALTER TABLE ONLY contact.address DROP CONSTRAINT "address_contactId_fkey";
ALTER TABLE ONLY auth."servicePrivilege" DROP CONSTRAINT "servicePrivilege_accountId_fkey";
ALTER TABLE ONLY auth."roleMember" DROP CONSTRAINT "roleMember_userAccountId_fkey";
ALTER TABLE ONLY auth."roleMember" DROP CONSTRAINT "roleMember_roleId_fkey";
ALTER TABLE ONLY auth.privilege DROP CONSTRAINT "privilege_roleId_fkey";
DROP INDEX "recordsManagement"."recordsManagement_archive_parentArchiveId_idx";
DROP INDEX "recordsManagement"."recordsManagement_archive_originatorOwnerOrgId_idx";
DROP INDEX "recordsManagement"."recordsManagement_archive_originatorOrgRegNumber_idx";
DROP INDEX "recordsManagement"."recordsManagement_archive_originatorArchiveId_idx";
DROP INDEX "recordsManagement"."recordsManagement_archive_originatingDate_idx";
DROP INDEX "recordsManagement"."recordsManagement_archive_descriptionClass_idx";
DROP INDEX "recordsManagement"."recordsManagement_archive_archiverOrgRegNumber_idx";
DROP INDEX "recordsManagement"."recordsManagement_archiveRelationship_relatedArchiveId_idx";
DROP INDEX "recordsManagement"."recordsManagement_archiveRelationship_archiveId_idx";
DROP INDEX "recordsManagement".archive_to_tsvector_idx;
DROP INDEX "recordsManagement".archive_status_idx;
DROP INDEX "recordsManagement"."archive_originatorOrgRegNumber_originatorArchiveId_idx";
DROP INDEX "recordsManagement"."archive_filePlanPosition_idx";
DROP INDEX "recordsManagement"."archive_disposalDate_idx";
DROP INDEX "recordsManagement"."archive_archivalProfileReference_idx";
DROP INDEX medona."medona_unitIdentifier_objectClass_idx";
DROP INDEX medona."medona_unitIdentifier_messageId_idx";
DROP INDEX medona.medona_message_status_active_idx;
DROP INDEX medona."medona_message_recipientOrgRegNumber_idx";
DROP INDEX medona.medona_message_date_idx;
DROP INDEX "lifeCycle"."lifeCycle_event_objectClass_objectId_idx";
DROP INDEX "lifeCycle"."lifeCycle_event_objectClass_idx";
DROP INDEX "lifeCycle"."lifeCycle_event_instanceName_idx";
DROP INDEX "lifeCycle"."lifeCycle_event_eventType_idx";
DROP INDEX "lifeCycle".event_timestamp_idx;
DROP INDEX "lifeCycle"."event_objectId_idx";
DROP INDEX "filePlan"."filePlan_folder_ownerOrgRegNumber_idx";
DROP INDEX "digitalResource"."digitalResource_digitalResource_relatedResId__relationshipType_";
DROP INDEX "digitalResource"."digitalResource_digitalResource_archiveId_idx";
DROP INDEX "batchProcessing"."batchProcessing_logScheduling_schedulingId_idx";
DROP INDEX audit."audit_event_instanceName_idx";
DROP INDEX audit."audit_event_eventDate_idx";
ALTER TABLE ONLY "recordsManagement"."storageRule" DROP CONSTRAINT "storageRule_pkey";
ALTER TABLE ONLY "recordsManagement"."serviceLevel" DROP CONSTRAINT "serviceLevel_reference_key";
ALTER TABLE ONLY "recordsManagement"."serviceLevel" DROP CONSTRAINT "serviceLevel_pkey";
ALTER TABLE ONLY "recordsManagement"."retentionRule" DROP CONSTRAINT "retentionRule_pkey";
ALTER TABLE ONLY "recordsManagement".log DROP CONSTRAINT log_pkey;
ALTER TABLE ONLY "recordsManagement"."descriptionField" DROP CONSTRAINT "descriptionField_pkey";
ALTER TABLE ONLY "recordsManagement"."descriptionClass" DROP CONSTRAINT "descriptionClass_pkey";
ALTER TABLE ONLY "recordsManagement".archive DROP CONSTRAINT archive_pkey;
ALTER TABLE ONLY "recordsManagement"."archiveRelationship" DROP CONSTRAINT "archiveRelationship_pkey";
ALTER TABLE ONLY "recordsManagement"."archiveDescription" DROP CONSTRAINT "archiveDescription_pkey";
ALTER TABLE ONLY "recordsManagement"."archivalProfile" DROP CONSTRAINT "archivalProfile_reference_key";
ALTER TABLE ONLY "recordsManagement"."archivalProfile" DROP CONSTRAINT "archivalProfile_pkey";
ALTER TABLE ONLY "recordsManagement"."archivalProfileContents" DROP CONSTRAINT "archivalProfileContents_pkey";
ALTER TABLE ONLY "recordsManagement"."accessRule" DROP CONSTRAINT "accessRule_pkey";
ALTER TABLE ONLY organization."userPosition" DROP CONSTRAINT "userPosition_pkey";
ALTER TABLE ONLY organization."servicePosition" DROP CONSTRAINT "servicePosition_pkey";
ALTER TABLE ONLY organization.organization DROP CONSTRAINT "organization_taxIdentifier_key";
ALTER TABLE ONLY organization.organization DROP CONSTRAINT "organization_registrationNumber_key";
ALTER TABLE ONLY organization.organization DROP CONSTRAINT organization_pkey;
ALTER TABLE ONLY organization."orgType" DROP CONSTRAINT "orgType_pkey";
ALTER TABLE ONLY organization."orgContact" DROP CONSTRAINT "orgContact_pkey";
ALTER TABLE ONLY organization."archivalProfileAccess" DROP CONSTRAINT "archivalProfileAccess_pkey";
ALTER TABLE ONLY medona."unitIdentifier" DROP CONSTRAINT "unitIdentifier_messageId_objectClass_objectId_key";
ALTER TABLE ONLY medona.message DROP CONSTRAINT "message_type_reference_senderOrgRegNumber_key";
ALTER TABLE ONLY medona.message DROP CONSTRAINT message_pkey;
ALTER TABLE ONLY medona."messageComment" DROP CONSTRAINT "messageComment_pkey";
ALTER TABLE ONLY medona."messageComment" DROP CONSTRAINT "messageComment_messageId_comment_key";
ALTER TABLE ONLY medona."controlAuthority" DROP CONSTRAINT "controlAuthority_pkey";
ALTER TABLE ONLY medona."archivalAgreement" DROP CONSTRAINT "archivalAgreement_reference_key";
ALTER TABLE ONLY medona."archivalAgreement" DROP CONSTRAINT "archivalAgreement_pkey";
ALTER TABLE ONLY "lifeCycle".event DROP CONSTRAINT event_pkey;
ALTER TABLE ONLY "lifeCycle"."eventFormat" DROP CONSTRAINT "eventFormat_pkey";
ALTER TABLE ONLY "filePlan".folder DROP CONSTRAINT folder_pkey;
ALTER TABLE ONLY "filePlan".folder DROP CONSTRAINT "filePlan_name_parentFolderId_key";
ALTER TABLE ONLY "digitalResource".repository DROP CONSTRAINT "repository_repositoryUri_key";
ALTER TABLE ONLY "digitalResource".repository DROP CONSTRAINT "repository_repositoryReference_key";
ALTER TABLE ONLY "digitalResource".repository DROP CONSTRAINT repository_pkey;
ALTER TABLE ONLY "digitalResource".package DROP CONSTRAINT package_pkey;
ALTER TABLE ONLY "digitalResource"."digitalResource" DROP CONSTRAINT "digitalResource_pkey";
ALTER TABLE ONLY "digitalResource"."conversionRule" DROP CONSTRAINT "conversionRule_puid_key";
ALTER TABLE ONLY "digitalResource"."conversionRule" DROP CONSTRAINT "conversionRule_pkey";
ALTER TABLE ONLY "digitalResource"."contentType" DROP CONSTRAINT "contentType_pkey";
ALTER TABLE ONLY "digitalResource".cluster DROP CONSTRAINT cluster_pkey;
ALTER TABLE ONLY "digitalResource"."clusterRepository" DROP CONSTRAINT "clusterRepository_pkey";
ALTER TABLE ONLY "digitalResource".address DROP CONSTRAINT address_pkey;
ALTER TABLE ONLY contact.contact DROP CONSTRAINT contact_pkey;
ALTER TABLE ONLY contact.communication DROP CONSTRAINT communication_pkey;
ALTER TABLE ONLY contact.communication DROP CONSTRAINT "communication_contactId_purpose_comMeanCode_key";
ALTER TABLE ONLY contact."communicationMean" DROP CONSTRAINT "communicationMean_pkey";
ALTER TABLE ONLY contact."communicationMean" DROP CONSTRAINT "communicationMean_name_key";
ALTER TABLE ONLY contact.address DROP CONSTRAINT address_pkey;
ALTER TABLE ONLY contact.address DROP CONSTRAINT "address_contactId_purpose_key";
ALTER TABLE ONLY "batchProcessing".scheduling DROP CONSTRAINT scheduling_pkey;
ALTER TABLE ONLY "batchProcessing".notification DROP CONSTRAINT notification_pkey;
ALTER TABLE ONLY "batchProcessing"."logScheduling" DROP CONSTRAINT "logScheduling_pkey";
ALTER TABLE ONLY auth."servicePrivilege" DROP CONSTRAINT "servicePrivilege_accountId_serviceURI_key";
ALTER TABLE ONLY auth.role DROP CONSTRAINT role_pkey;
ALTER TABLE ONLY auth."roleMember" DROP CONSTRAINT "roleMember_roleId_userAccountId_key";
ALTER TABLE ONLY auth.privilege DROP CONSTRAINT "privilege_roleId_userStory_key";
ALTER TABLE ONLY auth.account DROP CONSTRAINT account_pkey;
ALTER TABLE ONLY auth.account DROP CONSTRAINT "account_accountName_key";
ALTER TABLE ONLY audit.event DROP CONSTRAINT event_pkey;
DROP TABLE "recordsManagement"."storageRule";
DROP TABLE "recordsManagement"."serviceLevel";
DROP TABLE "recordsManagement"."retentionRule";
DROP TABLE "recordsManagement".log;
DROP TABLE "recordsManagement"."descriptionField";
DROP TABLE "recordsManagement"."descriptionClass";
DROP TABLE "recordsManagement"."archiveRelationship";
DROP TABLE "recordsManagement"."archiveDescription";
DROP TABLE "recordsManagement".archive;
DROP TABLE "recordsManagement"."archivalProfileContents";
DROP TABLE "recordsManagement"."archivalProfile";
DROP TABLE "recordsManagement"."accessRule";
DROP TABLE organization."userPosition";
DROP TABLE organization."servicePosition";
DROP TABLE organization.organization;
DROP TABLE organization."orgType";
DROP TABLE organization."orgContact";
DROP TABLE organization."archivalProfileAccess";
DROP TABLE medona."unitIdentifier";
DROP TABLE medona."messageComment";
DROP TABLE medona.message;
DROP TABLE medona."controlAuthority";
DROP TABLE medona."archivalAgreement";
DROP TABLE "lifeCycle"."eventFormat";
DROP TABLE "lifeCycle".event;
DROP TABLE "filePlan"."position";
DROP TABLE "filePlan".folder;
DROP TABLE "digitalResource".repository;
DROP TABLE "digitalResource"."packedResource";
DROP TABLE "digitalResource".package;
DROP TABLE "digitalResource"."digitalResource";
DROP TABLE "digitalResource"."conversionRule";
DROP TABLE "digitalResource"."contentType";
DROP TABLE "digitalResource"."clusterRepository";
DROP TABLE "digitalResource".cluster;
DROP TABLE "digitalResource".address;
DROP TABLE contact.contact;
DROP TABLE contact."communicationMean";
DROP TABLE contact.communication;
DROP TABLE contact.address;
DROP TABLE "batchProcessing".scheduling;
DROP TABLE "batchProcessing".notification;
DROP TABLE "batchProcessing"."logScheduling";
DROP TABLE auth."servicePrivilege";
DROP TABLE auth."roleMember";
DROP TABLE auth.role;
DROP TABLE auth.privilege;
DROP TABLE auth.account;
DROP TABLE audit.event;
DROP SCHEMA "recordsManagement";
DROP SCHEMA organization;
DROP SCHEMA medona;
DROP SCHEMA "lifeCycle";
DROP SCHEMA "filePlan";
DROP SCHEMA "digitalResource";
DROP SCHEMA contact;
DROP SCHEMA "batchProcessing";
DROP SCHEMA auth;
DROP SCHEMA audit;
--
-- TOC entry 8 (class 2615 OID 536305)
-- Name: audit; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA audit;


ALTER SCHEMA audit OWNER TO maarch;

--
-- TOC entry 9 (class 2615 OID 536317)
-- Name: auth; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA auth;


ALTER SCHEMA auth OWNER TO maarch;

--
-- TOC entry 10 (class 2615 OID 536385)
-- Name: batchProcessing; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA "batchProcessing";


ALTER SCHEMA "batchProcessing" OWNER TO maarch;

--
-- TOC entry 11 (class 2615 OID 536412)
-- Name: contact; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA contact;


ALTER SCHEMA contact OWNER TO maarch;

--
-- TOC entry 14 (class 2615 OID 536697)
-- Name: digitalResource; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA "digitalResource";


ALTER SCHEMA "digitalResource" OWNER TO maarch;

--
-- TOC entry 15 (class 2615 OID 536827)
-- Name: filePlan; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA "filePlan";


ALTER SCHEMA "filePlan" OWNER TO maarch;

--
-- TOC entry 16 (class 2615 OID 536855)
-- Name: lifeCycle; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA "lifeCycle";


ALTER SCHEMA "lifeCycle" OWNER TO maarch;

--
-- TOC entry 12 (class 2615 OID 536467)
-- Name: medona; Type: SCHEMA; Schema: -; Owner: postgres
--

CREATE SCHEMA medona;


ALTER SCHEMA medona OWNER TO postgres;

--
-- TOC entry 17 (class 2615 OID 536879)
-- Name: organization; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA organization;


ALTER SCHEMA organization OWNER TO maarch;

--
-- TOC entry 13 (class 2615 OID 536529)
-- Name: recordsManagement; Type: SCHEMA; Schema: -; Owner: maarch
--

CREATE SCHEMA "recordsManagement";


ALTER SCHEMA "recordsManagement" OWNER TO maarch;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 191 (class 1259 OID 536306)
-- Name: event; Type: TABLE; Schema: audit; Owner: maarch
--

CREATE TABLE audit.event (
                             "eventId" text NOT NULL,
                             "eventDate" timestamp without time zone NOT NULL,
                             "accountId" text NOT NULL,
                             "orgRegNumber" text,
                             "orgUnitRegNumber" text,
                             path text,
                             variables json,
                             input json,
                             output text,
                             status boolean DEFAULT true,
                             info text,
                             "instanceName" text
);


ALTER TABLE audit.event OWNER TO maarch;

--
-- TOC entry 193 (class 1259 OID 536327)
-- Name: account; Type: TABLE; Schema: auth; Owner: maarch
--

CREATE TABLE auth.account (
                              "accountId" text NOT NULL,
                              "accountName" text NOT NULL,
                              "displayName" text NOT NULL,
                              "accountType" text DEFAULT 'user'::text,
                              "emailAddress" text NOT NULL,
                              enabled boolean DEFAULT true,
                              password text,
                              "passwordChangeRequired" boolean DEFAULT true,
                              "passwordLastChange" timestamp without time zone,
                              locked boolean DEFAULT false,
                              "lockDate" timestamp without time zone,
                              "badPasswordCount" integer,
                              "lastLogin" timestamp without time zone,
                              "lastIp" text,
                              "replacingUserAccountId" text,
                              "firstName" text,
                              "lastName" text,
                              title text,
                              salt text,
                              "tokenDate" timestamp without time zone,
                              authentication jsonb,
                              preferences jsonb,
                              "ownerOrgId" text,
                              "isAdmin" boolean
);


ALTER TABLE auth.account OWNER TO maarch;

--
-- TOC entry 195 (class 1259 OID 536359)
-- Name: privilege; Type: TABLE; Schema: auth; Owner: maarch
--

CREATE TABLE auth.privilege (
                                "roleId" text,
                                "userStory" text
);


ALTER TABLE auth.privilege OWNER TO maarch;

--
-- TOC entry 192 (class 1259 OID 536318)
-- Name: role; Type: TABLE; Schema: auth; Owner: maarch
--

CREATE TABLE auth.role (
                           "roleId" text NOT NULL,
                           "roleName" text NOT NULL,
                           description text,
                           enabled boolean DEFAULT true
);


ALTER TABLE auth.role OWNER TO maarch;

--
-- TOC entry 194 (class 1259 OID 536341)
-- Name: roleMember; Type: TABLE; Schema: auth; Owner: maarch
--

CREATE TABLE auth."roleMember" (
                                   "roleId" text,
                                   "userAccountId" text NOT NULL
);


ALTER TABLE auth."roleMember" OWNER TO maarch;

--
-- TOC entry 196 (class 1259 OID 536372)
-- Name: servicePrivilege; Type: TABLE; Schema: auth; Owner: maarch
--

CREATE TABLE auth."servicePrivilege" (
                                         "accountId" text,
                                         "serviceURI" text
);


ALTER TABLE auth."servicePrivilege" OWNER TO maarch;

--
-- TOC entry 198 (class 1259 OID 536394)
-- Name: logScheduling; Type: TABLE; Schema: batchProcessing; Owner: maarch
--

CREATE TABLE "batchProcessing"."logScheduling" (
                                                   "logId" text NOT NULL,
                                                   "schedulingId" text NOT NULL,
                                                   "executedBy" text NOT NULL,
                                                   "launchedBy" text NOT NULL,
                                                   "logDate" timestamp without time zone NOT NULL,
                                                   status boolean DEFAULT true,
                                                   info text
);


ALTER TABLE "batchProcessing"."logScheduling" OWNER TO maarch;

--
-- TOC entry 199 (class 1259 OID 536403)
-- Name: notification; Type: TABLE; Schema: batchProcessing; Owner: maarch
--

CREATE TABLE "batchProcessing".notification (
                                                "notificationId" text NOT NULL,
                                                receivers text NOT NULL,
                                                message text NOT NULL,
                                                title text NOT NULL,
                                                "createdDate" timestamp without time zone NOT NULL,
                                                "createdBy" text,
                                                status text NOT NULL,
                                                "sendDate" timestamp without time zone,
                                                "sendBy" text
);


ALTER TABLE "batchProcessing".notification OWNER TO maarch;

--
-- TOC entry 197 (class 1259 OID 536386)
-- Name: scheduling; Type: TABLE; Schema: batchProcessing; Owner: maarch
--

CREATE TABLE "batchProcessing".scheduling (
                                              "schedulingId" text NOT NULL,
                                              name text NOT NULL,
                                              "taskId" text NOT NULL,
                                              frequency text NOT NULL,
                                              parameters text,
                                              "executedBy" text NOT NULL,
                                              "lastExecution" timestamp without time zone,
                                              "nextExecution" timestamp without time zone,
                                              status text
);


ALTER TABLE "batchProcessing".scheduling OWNER TO maarch;

--
-- TOC entry 201 (class 1259 OID 536422)
-- Name: address; Type: TABLE; Schema: contact; Owner: maarch
--

CREATE TABLE contact.address (
                                 "addressId" text NOT NULL,
                                 "contactId" text NOT NULL,
                                 purpose text NOT NULL,
                                 room text,
                                 floor text,
                                 building text,
                                 number text,
                                 street text,
                                 "postBox" text,
                                 block text,
                                 "citySubDivision" text,
                                 "postCode" text,
                                 city text,
                                 country text
);


ALTER TABLE contact.address OWNER TO maarch;

--
-- TOC entry 203 (class 1259 OID 536447)
-- Name: communication; Type: TABLE; Schema: contact; Owner: maarch
--

CREATE TABLE contact.communication (
                                       "communicationId" text NOT NULL,
                                       "contactId" text NOT NULL,
                                       purpose text NOT NULL,
                                       "comMeanCode" text NOT NULL,
                                       value text NOT NULL,
                                       info text
);


ALTER TABLE contact.communication OWNER TO maarch;

--
-- TOC entry 202 (class 1259 OID 536437)
-- Name: communicationMean; Type: TABLE; Schema: contact; Owner: maarch
--

CREATE TABLE contact."communicationMean" (
                                             code text NOT NULL,
                                             name text NOT NULL,
                                             enabled boolean
);


ALTER TABLE contact."communicationMean" OWNER TO maarch;

--
-- TOC entry 200 (class 1259 OID 536413)
-- Name: contact; Type: TABLE; Schema: contact; Owner: maarch
--

CREATE TABLE contact.contact (
                                 "contactId" text NOT NULL,
                                 "contactType" text DEFAULT 'person'::text NOT NULL,
                                 "orgName" text,
                                 "firstName" text,
                                 "lastName" text,
                                 title text,
                                 function text,
                                 service text,
                                 "displayName" text
);


ALTER TABLE contact.contact OWNER TO maarch;

--
-- TOC entry 224 (class 1259 OID 536741)
-- Name: address; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource".address (
                                           "resId" text NOT NULL,
                                           "repositoryId" text NOT NULL,
                                           path text NOT NULL,
                                           "lastIntegrityCheck" timestamp without time zone,
                                           "integrityCheckResult" boolean,
                                           packed boolean DEFAULT false,
                                           created timestamp without time zone NOT NULL
);


ALTER TABLE "digitalResource".address OWNER TO maarch;

--
-- TOC entry 221 (class 1259 OID 536698)
-- Name: cluster; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource".cluster (
                                           "clusterId" text NOT NULL,
                                           "clusterName" text,
                                           "clusterDescription" text
);


ALTER TABLE "digitalResource".cluster OWNER TO maarch;

--
-- TOC entry 225 (class 1259 OID 536760)
-- Name: clusterRepository; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource"."clusterRepository" (
                                                       "clusterId" text NOT NULL,
                                                       "repositoryId" text NOT NULL,
                                                       "writePriority" integer,
                                                       "readPriority" integer,
                                                       "deletePriority" integer
);


ALTER TABLE "digitalResource"."clusterRepository" OWNER TO maarch;

--
-- TOC entry 228 (class 1259 OID 536807)
-- Name: contentType; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource"."contentType" (
                                                 name text NOT NULL,
                                                 mediatype text NOT NULL,
                                                 description text,
                                                 puids text,
                                                 "validationMode" text,
                                                 "conversionMode" text,
                                                 "textExtractionMode" text,
                                                 "metadataExtractionMode" text
);


ALTER TABLE "digitalResource"."contentType" OWNER TO maarch;

--
-- TOC entry 229 (class 1259 OID 536815)
-- Name: conversionRule; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource"."conversionRule" (
                                                    "conversionRuleId" text NOT NULL,
                                                    puid text NOT NULL,
                                                    "conversionService" text NOT NULL,
                                                    "targetPuid" text NOT NULL
);


ALTER TABLE "digitalResource"."conversionRule" OWNER TO maarch;

--
-- TOC entry 222 (class 1259 OID 536706)
-- Name: digitalResource; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource"."digitalResource" (
                                                     "archiveId" text NOT NULL,
                                                     "resId" text NOT NULL,
                                                     "clusterId" text NOT NULL,
                                                     size bigint NOT NULL,
                                                     puid text,
                                                     mimetype text,
                                                     hash text,
                                                     "hashAlgorithm" text,
                                                     "fileExtension" text,
                                                     "fileName" text,
                                                     "mediaInfo" text,
                                                     created timestamp without time zone NOT NULL,
                                                     updated timestamp without time zone,
                                                     "relatedResId" text,
                                                     "relationshipType" text
);


ALTER TABLE "digitalResource"."digitalResource" OWNER TO maarch;

--
-- TOC entry 226 (class 1259 OID 536778)
-- Name: package; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource".package (
                                           "packageId" text NOT NULL,
                                           method text NOT NULL
);


ALTER TABLE "digitalResource".package OWNER TO maarch;

--
-- TOC entry 227 (class 1259 OID 536791)
-- Name: packedResource; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource"."packedResource" (
                                                    "packageId" text NOT NULL,
                                                    "resId" text NOT NULL,
                                                    name text NOT NULL
);


ALTER TABLE "digitalResource"."packedResource" OWNER TO maarch;

--
-- TOC entry 223 (class 1259 OID 536729)
-- Name: repository; Type: TABLE; Schema: digitalResource; Owner: maarch
--

CREATE TABLE "digitalResource".repository (
                                              "repositoryId" text NOT NULL,
                                              "repositoryName" text NOT NULL,
                                              "repositoryReference" text NOT NULL,
                                              "repositoryType" text NOT NULL,
                                              "repositoryUri" text NOT NULL,
                                              parameters text,
                                              "maxSize" integer,
                                              enabled boolean
);


ALTER TABLE "digitalResource".repository OWNER TO maarch;

--
-- TOC entry 230 (class 1259 OID 536828)
-- Name: folder; Type: TABLE; Schema: filePlan; Owner: maarch
--

CREATE TABLE "filePlan".folder (
                                   "folderId" text NOT NULL,
                                   name text NOT NULL,
                                   "parentFolderId" text,
                                   description text,
                                   "ownerOrgRegNumber" text,
                                   closed boolean
);


ALTER TABLE "filePlan".folder OWNER TO maarch;

--
-- TOC entry 231 (class 1259 OID 536843)
-- Name: position; Type: TABLE; Schema: filePlan; Owner: maarch
--

CREATE TABLE "filePlan"."position" (
                                       "folderId" text NOT NULL,
                                       "archiveId" text NOT NULL
);


ALTER TABLE "filePlan"."position" OWNER TO maarch;

--
-- TOC entry 232 (class 1259 OID 536856)
-- Name: event; Type: TABLE; Schema: lifeCycle; Owner: maarch
--

CREATE TABLE "lifeCycle".event (
                                   "eventId" text NOT NULL,
                                   "eventType" text NOT NULL,
                                   "timestamp" timestamp without time zone NOT NULL,
                                   "instanceName" text NOT NULL,
                                   "orgRegNumber" text,
                                   "orgUnitRegNumber" text,
                                   "accountId" text,
                                   "objectClass" text NOT NULL,
                                   "objectId" text NOT NULL,
                                   "operationResult" boolean,
                                   description text,
                                   "eventInfo" text
);


ALTER TABLE "lifeCycle".event OWNER TO maarch;

--
-- TOC entry 233 (class 1259 OID 536864)
-- Name: eventFormat; Type: TABLE; Schema: lifeCycle; Owner: maarch
--

CREATE TABLE "lifeCycle"."eventFormat" (
                                           type text NOT NULL,
                                           format text NOT NULL,
                                           message text NOT NULL,
                                           notification boolean DEFAULT false
);


ALTER TABLE "lifeCycle"."eventFormat" OWNER TO maarch;

--
-- TOC entry 204 (class 1259 OID 536468)
-- Name: archivalAgreement; Type: TABLE; Schema: medona; Owner: maarch
--

CREATE TABLE medona."archivalAgreement" (
                                            "archivalAgreementId" text NOT NULL,
                                            name text NOT NULL,
                                            reference text NOT NULL,
                                            description text,
                                            "archivalProfileReference" text,
                                            "serviceLevelReference" text,
                                            "archiverOrgRegNumber" text NOT NULL,
                                            "depositorOrgRegNumber" text NOT NULL,
                                            "originatorOrgIds" text,
                                            "beginDate" date,
                                            "endDate" date,
                                            enabled boolean,
                                            "allowedFormats" text,
                                            "maxSizeAgreement" integer,
                                            "maxSizeTransfer" integer,
                                            "maxSizeDay" integer,
                                            "maxSizeWeek" integer,
                                            "maxSizeMonth" integer,
                                            "maxSizeYear" integer,
                                            signed boolean,
                                            "autoTransferAcceptance" boolean,
                                            "processSmallArchive" boolean
);


ALTER TABLE medona."archivalAgreement" OWNER TO maarch;

--
-- TOC entry 208 (class 1259 OID 536516)
-- Name: controlAuthority; Type: TABLE; Schema: medona; Owner: maarch
--

CREATE TABLE medona."controlAuthority" (
                                           "originatorOrgUnitId" text NOT NULL,
                                           "controlAuthorityOrgUnitId" text NOT NULL
);


ALTER TABLE medona."controlAuthority" OWNER TO maarch;

--
-- TOC entry 205 (class 1259 OID 536478)
-- Name: message; Type: TABLE; Schema: medona; Owner: maarch
--

CREATE TABLE medona.message (
                                "messageId" text NOT NULL,
                                schema text,
                                type text NOT NULL,
                                status text NOT NULL,
                                date timestamp without time zone NOT NULL,
                                reference text NOT NULL,
                                "accountId" text,
                                "senderOrgRegNumber" text NOT NULL,
                                "senderOrgName" text,
                                "recipientOrgRegNumber" text NOT NULL,
                                "recipientOrgName" text,
                                "archivalAgreementReference" text,
                                "replyCode" text,
                                "operationDate" timestamp without time zone,
                                "receptionDate" timestamp without time zone,
                                "relatedReference" text,
                                "requestReference" text,
                                "replyReference" text,
                                "authorizationReference" text,
                                "authorizationReason" text,
                                "authorizationRequesterOrgRegNumber" text,
                                derogation boolean,
                                "dataObjectCount" integer,
                                size numeric,
                                data text,
                                path text,
                                active boolean,
                                archived boolean,
                                "isIncoming" boolean,
                                comment text
);


ALTER TABLE medona.message OWNER TO maarch;

--
-- TOC entry 206 (class 1259 OID 536488)
-- Name: messageComment; Type: TABLE; Schema: medona; Owner: maarch
--

CREATE TABLE medona."messageComment" (
                                         "messageId" text,
                                         comment text,
                                         "commentId" text NOT NULL
);


ALTER TABLE medona."messageComment" OWNER TO maarch;

--
-- TOC entry 207 (class 1259 OID 536503)
-- Name: unitIdentifier; Type: TABLE; Schema: medona; Owner: maarch
--

CREATE TABLE medona."unitIdentifier" (
                                         "messageId" text NOT NULL,
                                         "objectClass" text NOT NULL,
                                         "objectId" text NOT NULL
);


ALTER TABLE medona."unitIdentifier" OWNER TO maarch;

--
-- TOC entry 239 (class 1259 OID 536949)
-- Name: archivalProfileAccess; Type: TABLE; Schema: organization; Owner: maarch
--

CREATE TABLE organization."archivalProfileAccess" (
                                                      "orgId" text NOT NULL,
                                                      "archivalProfileReference" text NOT NULL,
                                                      "originatorAccess" boolean DEFAULT true,
                                                      "serviceLevelReference" text,
                                                      "userAccess" jsonb
);


ALTER TABLE organization."archivalProfileAccess" OWNER TO maarch;

--
-- TOC entry 238 (class 1259 OID 536936)
-- Name: orgContact; Type: TABLE; Schema: organization; Owner: maarch
--

CREATE TABLE organization."orgContact" (
                                           "contactId" text NOT NULL,
                                           "orgId" text NOT NULL,
                                           "isSelf" boolean
);


ALTER TABLE organization."orgContact" OWNER TO maarch;

--
-- TOC entry 234 (class 1259 OID 536880)
-- Name: orgType; Type: TABLE; Schema: organization; Owner: maarch
--

CREATE TABLE organization."orgType" (
                                        code text NOT NULL,
                                        name text
);


ALTER TABLE organization."orgType" OWNER TO maarch;

--
-- TOC entry 235 (class 1259 OID 536888)
-- Name: organization; Type: TABLE; Schema: organization; Owner: maarch
--

CREATE TABLE organization.organization (
                                           "orgId" text NOT NULL,
                                           "orgName" text NOT NULL,
                                           "otherOrgName" text,
                                           "displayName" text NOT NULL,
                                           "registrationNumber" text NOT NULL,
                                           "beginDate" date,
                                           "endDate" date,
                                           "legalClassification" text,
                                           "businessType" text,
                                           description text,
                                           "orgTypeCode" text,
                                           "orgRoleCodes" text,
                                           "taxIdentifier" text,
                                           "parentOrgId" text,
                                           "ownerOrgId" text,
                                           "isOrgUnit" boolean
);


ALTER TABLE organization.organization OWNER TO maarch;

--
-- TOC entry 237 (class 1259 OID 536928)
-- Name: servicePosition; Type: TABLE; Schema: organization; Owner: maarch
--

CREATE TABLE organization."servicePosition" (
                                                "serviceAccountId" text NOT NULL,
                                                "orgId" text NOT NULL
);


ALTER TABLE organization."servicePosition" OWNER TO maarch;

--
-- TOC entry 236 (class 1259 OID 536915)
-- Name: userPosition; Type: TABLE; Schema: organization; Owner: maarch
--

CREATE TABLE organization."userPosition" (
                                             "userAccountId" text NOT NULL,
                                             "orgId" text NOT NULL,
                                             function text,
                                             "default" boolean
);


ALTER TABLE organization."userPosition" OWNER TO maarch;

--
-- TOC entry 209 (class 1259 OID 536530)
-- Name: accessRule; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."accessRule" (
                                                  code text NOT NULL,
                                                  duration text,
                                                  description text NOT NULL
);


ALTER TABLE "recordsManagement"."accessRule" OWNER TO maarch;

--
-- TOC entry 211 (class 1259 OID 536546)
-- Name: archivalProfile; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."archivalProfile" (
                                                       "archivalProfileId" text NOT NULL,
                                                       reference text NOT NULL,
                                                       name text NOT NULL,
                                                       "descriptionSchema" text,
                                                       "descriptionClass" text,
                                                       "retentionStartDate" text,
                                                       "retentionRuleCode" text,
                                                       description text,
                                                       "accessRuleCode" text,
                                                       "acceptUserIndex" boolean DEFAULT false,
                                                       "acceptArchiveWithoutProfile" boolean DEFAULT true,
                                                       "fileplanLevel" text,
                                                       "processingStatuses" jsonb
);


ALTER TABLE "recordsManagement"."archivalProfile" OWNER TO maarch;

--
-- TOC entry 212 (class 1259 OID 536568)
-- Name: archivalProfileContents; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."archivalProfileContents" (
                                                               "parentProfileId" text NOT NULL,
                                                               "containedProfileId" text NOT NULL
);


ALTER TABLE "recordsManagement"."archivalProfileContents" OWNER TO maarch;

--
-- TOC entry 216 (class 1259 OID 536621)
-- Name: archive; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement".archive (
                                             "archiveId" text NOT NULL,
                                             "originatorArchiveId" text,
                                             "depositorArchiveId" text,
                                             "archiverArchiveId" text,
                                             "archiveName" text,
                                             "storagePath" text,
                                             "filePlanPosition" text,
                                             "fileplanLevel" text,
                                             "originatingDate" date,
                                             "descriptionClass" text,
                                             description jsonb,
                                             text text,
                                             "originatorOrgRegNumber" text NOT NULL,
                                             "originatorOwnerOrgId" text,
                                             "originatorOwnerOrgRegNumber" text,
                                             "depositorOrgRegNumber" text,
                                             "archiverOrgRegNumber" text,
                                             "userOrgRegNumbers" text,
                                             "archivalProfileReference" text,
                                             "archivalAgreementReference" text,
                                             "serviceLevelReference" text,
                                             "retentionRuleCode" text,
                                             "retentionStartDate" date,
                                             "retentionDuration" text,
                                             "finalDisposition" text,
                                             "disposalDate" date,
                                             "retentionRuleStatus" text,
                                             "accessRuleCode" text,
                                             "accessRuleDuration" text,
                                             "accessRuleStartDate" date,
                                             "accessRuleComDate" date,
                                             "storageRuleCode" text,
                                             "storageRuleDuration" text,
                                             "storageRuleStartDate" date,
                                             "storageRuleEndDate" date,
                                             "classificationRuleCode" text,
                                             "classificationRuleDuration" text,
                                             "classificationRuleStartDate" date,
                                             "classificationEndDate" date,
                                             "classificationLevel" text,
                                             "classificationOwner" text,
                                             "depositDate" timestamp without time zone NOT NULL,
                                             "lastCheckDate" timestamp without time zone,
                                             "lastDeliveryDate" timestamp without time zone,
                                             "lastModificationDate" timestamp without time zone,
                                             status text NOT NULL,
                                             "processingStatus" text,
                                             "parentArchiveId" text,
                                             "fullTextIndexation" text DEFAULT 'none'::text
);


ALTER TABLE "recordsManagement".archive OWNER TO maarch;

--
-- TOC entry 214 (class 1259 OID 536595)
-- Name: archiveDescription; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."archiveDescription" (
                                                          "archivalProfileId" text NOT NULL,
                                                          "fieldName" text NOT NULL,
                                                          required boolean,
                                                          "position" integer,
                                                          "isImmutable" boolean DEFAULT false,
                                                          "isRetained" boolean DEFAULT true,
                                                          "isInList" boolean DEFAULT false
);


ALTER TABLE "recordsManagement"."archiveDescription" OWNER TO maarch;

--
-- TOC entry 217 (class 1259 OID 536646)
-- Name: archiveRelationship; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."archiveRelationship" (
                                                           "archiveId" text NOT NULL,
                                                           "relatedArchiveId" text NOT NULL,
                                                           "typeCode" text NOT NULL,
                                                           description jsonb
);


ALTER TABLE "recordsManagement"."archiveRelationship" OWNER TO maarch;

--
-- TOC entry 219 (class 1259 OID 536672)
-- Name: descriptionClass; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."descriptionClass" (
                                                        name text NOT NULL,
                                                        label text NOT NULL
);


ALTER TABLE "recordsManagement"."descriptionClass" OWNER TO maarch;

--
-- TOC entry 213 (class 1259 OID 536586)
-- Name: descriptionField; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."descriptionField" (
                                                        name text NOT NULL,
                                                        label text,
                                                        type text,
                                                        "default" text,
                                                        "minLength" smallint,
                                                        "maxLength" smallint,
                                                        "minValue" numeric,
                                                        "maxValue" numeric,
                                                        enumeration text,
                                                        facets jsonb,
                                                        pattern text,
                                                        "isArray" boolean DEFAULT false
);


ALTER TABLE "recordsManagement"."descriptionField" OWNER TO maarch;

--
-- TOC entry 218 (class 1259 OID 536664)
-- Name: log; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement".log (
                                         "archiveId" text NOT NULL,
                                         "fromDate" timestamp without time zone NOT NULL,
                                         "toDate" timestamp without time zone NOT NULL,
                                         "processId" text,
                                         "processName" text,
                                         type text NOT NULL,
                                         "ownerOrgRegNumber" text
);


ALTER TABLE "recordsManagement".log OWNER TO maarch;

--
-- TOC entry 210 (class 1259 OID 536538)
-- Name: retentionRule; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."retentionRule" (
                                                     code text NOT NULL,
                                                     duration text NOT NULL,
                                                     "finalDisposition" text,
                                                     description text,
                                                     label text NOT NULL,
                                                     "implementationDate" date
);


ALTER TABLE "recordsManagement"."retentionRule" OWNER TO maarch;

--
-- TOC entry 215 (class 1259 OID 536611)
-- Name: serviceLevel; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."serviceLevel" (
                                                    "serviceLevelId" text NOT NULL,
                                                    reference text NOT NULL,
                                                    "digitalResourceClusterId" text NOT NULL,
                                                    control text,
                                                    "default" boolean,
                                                    "samplingFrequency" integer,
                                                    "samplingRate" integer
);


ALTER TABLE "recordsManagement"."serviceLevel" OWNER TO maarch;

--
-- TOC entry 220 (class 1259 OID 536680)
-- Name: storageRule; Type: TABLE; Schema: recordsManagement; Owner: maarch
--

CREATE TABLE "recordsManagement"."storageRule" (
                                                   code text NOT NULL,
                                                   duration text NOT NULL,
                                                   description text,
                                                   label text
);


ALTER TABLE "recordsManagement"."storageRule" OWNER TO maarch;

--
-- TOC entry 2600 (class 0 OID 536306)
-- Dependencies: 191
-- Data for Name: event; Type: TABLE DATA; Schema: audit; Owner: maarch
--

COPY audit.event ("eventId", "eventDate", "accountId", "orgRegNumber", "orgUnitRegNumber", path, variables, input, output, status, info, "instanceName") FROM stdin;
maarchRM_pwoozk-02ae-n5osla	2019-08-23 09:57:20.068655	bblier	ACME	GIC	organization/organization/readTree	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwoozk-033b-m3fz9y	2019-08-23 09:57:20.082692	bblier	ACME	GIC	organization/orgType/readList	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwoozn-15a2-pb2snw	2019-08-23 09:57:23.553839	bblier	ACME	GIC	organization/organization/readTree	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwoozn-0236-qjbh5h	2019-08-23 09:57:23.566018	bblier	ACME	GIC	organization/orgType/readList	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwoozo-1a39-3xf5nc	2019-08-23 09:57:24.671346	bblier	ACME	GIC	auth/userAccount/readUserlist	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwoozo-208f-9pdnr4	2019-08-23 09:57:24.833559	bblier	ACME	GIC	organization/organization/readTree	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwoozr-0d27-ywesre	2019-08-23 09:57:27.336713	bblier	ACME	GIC	organization/organization/read_orgId_	{"orgId":"RH"}	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwoozu-1d33-tnna32	2019-08-23 09:57:30.74752	bblier	ACME	GIC	organization/organization/deleteArchivalprofileaccess	\N	{"orgId":"RH","archivalProfileReference":"NOTSER"}	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwoozx-03da-ws32k8	2019-08-23 09:57:33.098621	bblier	ACME	GIC	organization/organization/createArchivalprofileaccess	\N	[]	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop01-0ef4-qhvsc8	2019-08-23 09:57:37.382837	bblier	ACME	GIC	auth/role/readIndex	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop05-1f16-i77i3s	2019-08-23 09:57:41.795858	bblier	ACME	GIC	auth/role/read_roleId_	{"roleId":"CORRESPONDANT_ARCHIVES"}	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop05-1f6d-p14uf9	2019-08-23 09:57:41.804566	bblier	ACME	GIC	auth/publicUserStory/read	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop06-0211-71s23b	2019-08-23 09:57:42.05292	bblier	ACME	GIC	auth/userAccount/readUserlist	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop0b-25db-xwsgn6	2019-08-23 09:57:47.969171	bblier	ACME	GIC	auth/role/update_roleId_	{"roleId":"CORRESPONDANT_ARCHIVES"}	{"role":"{\\"roleId\\":\\"CORRESPONDANT_ARCHIVES\\",\\"roleName\\":\\"Correspondant d'archives\\",\\"description\\":\\"Groupe des archivistes \\\\/ records managers \\\\/ r\\\\u00e9f\\\\u00e9rents d'archives \\\\/ administrateur fonctionnels\\",\\"enabled\\":true,\\"privileges\\":[\\"adminArchive\\\\/*\\",\\"adminFunc\\\\/AdminArchivalProfileAccess\\",\\"adminFunc\\\\/adminAuthorization\\",\\"adminFunc\\\\/adminOrgContact\\",\\"adminFunc\\\\/adminOrgUser\\",\\"adminFunc\\\\/adminOrganization\\",\\"adminFunc\\\\/adminServiceaccount\\",\\"adminFunc\\\\/adminUseraccount\\",\\"archiveDeposit\\\\/*\\",\\"archiveManagement\\\\/*\\",\\"destruction\\\\/*\\",\\"journal\\\\/lifeCycleJournal\\",\\"journal\\\\/searchLogArchive\\"],\\"roleMembers\\":[\\"bblier\\",\\"ccharles\\",\\"ddenis\\"]}"}	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop0c-045b-mpf1ib	2019-08-23 09:57:48.111544	bblier	ACME	GIC	auth/role/readIndex	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop0e-0231-s0tljr	2019-08-23 09:57:50.561039	bblier	ACME	GIC	auth/role/readIndex	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop0h-2351-f0vqir	2019-08-23 09:57:53.904158	bblier	ACME	GIC	auth/serviceAccount/readSearch	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop0j-119a-xasojb	2019-08-23 09:57:55.450639	bblier	ACME	GIC	auth/serviceAccount/readNewservice	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop0j-1b47-uwwh4c	2019-08-23 09:57:55.698277	bblier	ACME	GIC	organization/organization/readTodisplay	\N	{"ownerOrg":false,"orgUnit":true}	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop0j-1f70-u560i3	2019-08-23 09:57:55.804808	bblier	ACME	GIC	organization/organization/readTodisplay	\N	{"ownerOrg":true,"orgUnit":false}	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop0t-268a-8o834k	2019-08-23 09:58:05.986632	bblier	ACME	GIC	auth/serviceAccount/create	\N	[]	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop0u-06bc-jhc43j	2019-08-23 09:58:06.172448	bblier	ACME	GIC	auth/serviceAccount/readSearch	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop0v-1890-jsl5ed	2019-08-23 09:58:07.628796	bblier	ACME	GIC	auth/serviceAccount/readNewservice	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop0v-22c5-964gxw	2019-08-23 09:58:07.890094	bblier	ACME	GIC	organization/organization/readTodisplay	\N	{"ownerOrg":true,"orgUnit":false}	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop1b-0d1d-if19pn	2019-08-23 09:58:23.335704	bblier	ACME	GIC	auth/serviceAccount/create	\N	{"orgId":"RH"}	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwop1b-13f3-w9yjxw	2019-08-23 09:58:23.510727	bblier	ACME	GIC	auth/serviceAccount/readSearch	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwopg2-1a8b-wemnr3	2019-08-23 10:07:14.679478	bblier	ACME	GIC	auth/serviceAccount/read_serviceAccountId_	{"serviceAccountId":"maarchRM_pwop0t-2658-1s2whn"}	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwopg2-2691-esfsux	2019-08-23 10:07:14.987315	bblier	ACME	GIC	organization/organization/readTodisplay	\N	{"ownerOrg":false,"orgUnit":true}	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwopg3-0445-jsq7co	2019-08-23 10:07:15.10933	bblier	ACME	GIC	organization/organization/readTodisplay	\N	{"ownerOrg":true,"orgUnit":false}	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwopg7-0332-yvlpjr	2019-08-23 10:07:19.818012	bblier	ACME	GIC	auth/serviceAccount/updateServicetoken_serviceAccountId_	{"serviceAccountId":"maarchRM_pwop0t-2658-1s2whn"}	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwopgv-0689-sbaq64	2019-08-23 10:07:43.167316	bblier	ACME	GIC	auth/serviceAccount/update	\N	[]	Object of class organization/organization identified by  was not found	f	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwousp-0c15-16466w	2019-08-23 12:02:49.309286	bblier	\N	\N	auth/authentication/createUserlogin	\N	{"userName":"bblier"}	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwousu-0486-7tk1w9	2019-08-23 12:02:54.115788	bblier	ACME	GIC	auth/serviceAccount/readSearch	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwousx-0c60-aqe6yy	2019-08-23 12:02:57.316835	bblier	ACME	GIC	auth/serviceAccount/read_serviceAccountId_	{"serviceAccountId":"maarchRM_pwop1b-0ced-pmp8jy"}	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwousx-1773-uv6jn8	2019-08-23 12:02:57.600293	bblier	ACME	GIC	organization/organization/readTodisplay	\N	{"ownerOrg":true,"orgUnit":false}	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwout3-17be-xy4zxy	2019-08-23 12:03:03.607796	bblier	ACME	GIC	auth/serviceAccount/updateServicetoken_serviceAccountId_	{"serviceAccountId":"maarchRM_pwop1b-0ced-pmp8jy"}	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwoutb-09a4-6qaczy	2019-08-23 12:03:11.246791	bblier	ACME	GIC	auth/serviceAccount/update	\N	{"orgId":"RH"}	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
maarchRM_pwoutb-102d-8277o1	2019-08-23 12:03:11.414084	bblier	ACME	GIC	auth/serviceAccount/readSearch	\N	\N	\N	t	{"remoteIp":"127.0.0.1"}	maarchRM
\.


--
-- TOC entry 2602 (class 0 OID 536327)
-- Dependencies: 193
-- Data for Name: account; Type: TABLE DATA; Schema: auth; Owner: maarch
--

COPY auth.account ("accountId", "accountName", "displayName", "accountType", "emailAddress", enabled, password, "passwordChangeRequired", "passwordLastChange", locked, "lockDate", "badPasswordCount", "lastLogin", "lastIp", "replacingUserAccountId", "firstName", "lastName", title, salt, "tokenDate", authentication, preferences, "ownerOrgId", "isAdmin") FROM stdin;
ccordy	ccordy	Chloé CORDY	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Chloé	CORDY	Mme.	\N	\N	\N	\N	\N	f
ccox	ccox	Courtney COX	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Courtney	COX	Mme.	\N	\N	\N	\N	\N	f
ddur	ddur	Dominique DUR	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Dominique	DUR	M.	\N	\N	\N	\N	\N	f
eerina	eerina	Edith ERINA	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Edith	ERINA	Mme.	\N	\N	\N	\N	\N	f
jjane	jjane	Jenny JANE	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Jenny	JANE	Mme.	\N	\N	\N	\N	\N	f
kkaar	kkaar	Katy KAAR	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Katy	KAAR	Mme.	\N	\N	\N	\N	\N	f
aastier	aastier	Alexandre ASTIER	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	2019-03-15 10:19:10.46925	127.0.0.1	\N	Alexandre	ASTIER	M.	\N	\N	\N	\N	\N	f
aackermann	aackermann	Amanda ACKERMANN	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Amanda	ACKERMANN	Mme.	\N	\N	\N	\N	\N	f
aadams	aadams	Amy ADAMS	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	2019-03-15 10:20:33.964708	127.0.0.1	\N	Amy	ADAMS	Mme.	\N	\N	\N	\N	\N	f
cchaplin	cchaplin	Charlie CHAPLIN	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Charlie	CHAPLIN	M.	\N	\N	\N	\N	\N	f
ppetit	ppetit	Patricia PETIT	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Patricia	PETIT	Mme.	\N	\N	\N	\N	\N	f
ppreboist	ppreboist	Paul PREBOIST	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Paul	PREBOIST	M.	\N	\N	\N	\N	\N	f
ppruvost	ppruvost	Pierre PRUVOST	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Pierre	PRUVOST	M.	\N	\N	\N	\N	\N	f
rrenaud	rrenaud	Robert RENAUD	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Robert	RENAUD	M.	\N	\N	\N	\N	\N	f
rreynolds	rreynolds	Ryan REYNOLDS	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Ryan	REYNOLDS	M.	\N	\N	\N	\N	\N	f
ssaporta	ssaporta	Sabrina SAPORTA	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Sabrina	SAPORTA	Mme.	\N	\N	\N	\N	\N	f
ssissoko	ssissoko	Sylvain SISSOKO	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Sylvain	SISSOKO	M.	\N	\N	\N	\N	\N	f
sstallone	sstallone	Sylvester STALLONE	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Sylvester	STALLONE	M.	\N	\N	\N	\N	\N	f
sstar	sstar	Suzanne STAR	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Suzanne	STAR	Mme.	\N	\N	\N	\N	\N	f
ttong	ttong	Tony TONG	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Tony	TONG	M.	\N	\N	\N	\N	\N	f
ccharles	ccharles	Charlotte CHARLES	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Charlotte	CHARLES	Mme.	\N	\N	\N	\N	\N	f
nnataly	nnataly	Nancy NATALY	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	2019-03-15 11:07:45.462923	127.0.0.1	\N	Nancy	NATALY	Mme.	\N	\N	\N	\N	\N	f
sstone	sstone	Sharon STONE	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	2019-03-15 08:30:27.732025	127.0.0.1	\N	Sharon	STONE	Mme.	\N	\N	\N	\N	\N	f
superadmin	superadmin	super admin	user	info@maarch.org	t	186cf774c97b60a1c106ef718d10970a6a06e06bef89553d9ae65d938a886eae	f	\N	f	\N	0	2019-03-15 07:43:51.395555	127.0.0.1	\N	Admin	Super	M.	\N	\N	\N	\N	\N	t
ttule	ttule	Thierry TULE	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Thierry	TULE	M.	\N	\N	\N	\N	\N	f
vvictoire	vvictoire	Victor VICTOIRE	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Victor	VICTOIRE	M.	\N	\N	\N	\N	\N	f
SystemDepositor	Systeme versant	Systeme versant	service		t	RJpzB36bmR+iuz/aHN9Zl9PDn8tZEs4mzsz9ORUXZpbMim/ilUMpE9FzYG3TW0Eii0Oy1PaFyJ35aBqcMU3gvAq4v0ZY0Z/r0cPVzbAaymd1UEnsAe3MjqGLt7BxvxiHJQ==	t	\N	f	\N	0	\N	\N	\N	\N	\N	\N	87eda47a7218326af0e3f4eaad7c2c22	2019-03-19 07:56:55.287696	\N	\N	\N	f
System	Systeme	Systeme	service		t	RJpzB36bmR+iuz/aHN9Zl9PDn8tZEs4mzsz9OXNeNIrej2+v3UMzAsF3PSzDUlZ73kPvgqbQmZvza0eZO062uQu57Rdah9z3mdbTh6NBiiR8FQTnW6eVgQ==	t	\N	f	\N	0	\N	\N	\N	\N	\N	\N	63ce15235abe97db0182e6857c1da763	2019-03-19 07:54:33.464846	\N	\N	\N	f
ggrand	ggrand	George GRAND	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	2019-03-15 07:57:01.613677	127.0.0.1	\N	George	GRAND	M.	\N	\N	\N	\N	\N	f
bbain	bbain	Barbara BAIN	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Barbara	BAIN	Mme.	\N	\N	\N	\N	\N	f
workflow_pod5l0-232e-0aggqt	bbardot	Brigitte BARDOT	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	t	2019-03-14 15:55:48.901327	f	\N	0	\N	\N	\N	Brigitte	BARDOT	Mme	\N	\N	\N	\N	\N	f
bboule	bboule	Bruno BOULE	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Bruno	BOULE	M.	\N	\N	\N	\N	\N	f
ccamus	ccamus	Cyril CAMUS	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Cyril	CAMUS	M.	\N	\N	\N	\N	\N	f
ddaull	ddaull	Denis DAULL	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Denis	DAULL	M.	\N	\N	\N	\N	\N	f
ddenis	ddenis	Didier DENIS	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Didier	DENIS	M.	\N	\N	\N	\N	\N	f
aalambic	aalambic	Alain ALAMBIC	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	2019-03-15 13:32:20.964652	127.0.0.1	\N	Alain	ALAMBIC	M.	\N	\N	\N	\N	\N	f
hhier	hhier	Hubert HIER	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Hubert	HIER	M.	\N	\N	\N	\N	\N	f
jjonasz	jjonasz	Jean JONASZ	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Jean	JONASZ	M.	\N	\N	\N	\N	\N	f
mmanfred	mmanfred	Martin MANFRED	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Martin	MANFRED	M.	\N	\N	\N	\N	\N	f
ppacioli	ppacioli	Paolo PACIOLI	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	\N	\N	\N	Paolo	PACIOLI	M.	\N	\N	\N	\N	\N	f
kkrach	kkrach	Kevin KRACH	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	2019-03-15 08:30:03.559857	127.0.0.1	\N	Kevin	KRACH	M.	\N	\N	\N	\N	\N	f
maarchRM_pwop0t-2658-1s2whn	CCFN	CCFN	service		t	RJpzB36bmR+iuz/aHN9Zl9PDn8tZEvA+3Mr7PAM/SYnIjGu9k0w3B5cqISiCUxRy0gT6l7HTxd7pYwDJM0y1vwy76EMP0cu5gcWFmeBLwHMwGF7qTa3Dnvib7f1isQDSIL6Kr82aL4pKFuAjmQ==	f	\N	f	\N	0	\N	\N	\N	\N	\N	\N	f074e6fdcce53413f3c868d5c40fb8d3	2019-08-23 10:07:19.813807	\N	\N	ACME	t
maarchRM_pwop1b-0ced-pmp8jy	CCFN USER	CCFN USER	service		t	RJpzB36bmR+iuz/aHN9Zl9PDn8tZEvA+3Mr7PAM/SYnIjGu8hUw1Usd2IWmcEVtwxQT6l7HTxd7pYwCfYR3jvArkv0QKgcy51MKAzuodnnA6GlnsQaqXnfbI7f1isQDSIL6Kr82aL4pKFuAjmQ==	f	\N	f	\N	0	\N	\N	\N	\N	\N	\N	0bfbf093df523a66195f52f2e872a673	2019-08-23 12:03:03.603355	\N	\N	ACME	f
bblier	bblier	Bernard BLIER	user	info@maarch.org	t	fffd2272074225feae229658e248b81529639e6199051abdeb49b6ed60adf13d	f	\N	f	\N	0	2019-08-23 12:02:49.300653	127.0.0.1	\N	Bernard	BLIER	M.	\N	\N	{"csrf": {"2019-08-23T12:03:11,207066Z": "cbe2f4d3cef4d0768b73aba7381958a442dbaf2efaefc8447de605d1dfe05522"}}	\N	\N	f
\.


--
-- TOC entry 2604 (class 0 OID 536359)
-- Dependencies: 195
-- Data for Name: privilege; Type: TABLE DATA; Schema: auth; Owner: maarch
--

COPY auth.privilege ("roleId", "userStory") FROM stdin;
workflow_pod3au-0037-nz1f8t	workflow/*
workflow_pod3au-0037-nz1f8t	archiveDeposit/*
workflow_pod3au-0037-nz1f8t	archiveManagement/checkIntegrity
workflow_pod3au-0037-nz1f8t	archiveManagement/filePlan
workflow_pod3au-0037-nz1f8t	archiveManagement/modifyDescription
workflow_pod3au-0037-nz1f8t	archiveManagement/modify
workflow_pod3au-0037-nz1f8t	archiveManagement/retrieve
workflow_pod3au-0037-nz1f8t	destruction/*
workflow_pod3c1-1bc0-zh5adq	workflow/*
workflow_pod3c1-1bc0-zh5adq	archiveDeposit/*
workflow_pod3c1-1bc0-zh5adq	archiveManagement/filePlan
workflow_pod3c1-1bc0-zh5adq	archiveManagement/modify
ADMIN	adminFunc/adminAuthorization
ADMIN	adminFunc/adminOrgUser
ADMIN	adminFunc/adminOrganization
ADMIN	adminFunc/adminServiceaccount
ADMIN	adminFunc/adminUseraccount
ADMIN	adminFunc/batchScheduling
ADMIN	adminTech/*
ADMIN	journal/audit
CORRESPONDANT_ARCHIVES	adminArchive/*
CORRESPONDANT_ARCHIVES	adminFunc/AdminArchivalProfileAccess
CORRESPONDANT_ARCHIVES	adminFunc/adminAuthorization
CORRESPONDANT_ARCHIVES	adminFunc/adminOrgContact
CORRESPONDANT_ARCHIVES	adminFunc/adminOrgUser
CORRESPONDANT_ARCHIVES	adminFunc/adminOrganization
CORRESPONDANT_ARCHIVES	adminFunc/adminServiceaccount
CORRESPONDANT_ARCHIVES	adminFunc/adminUseraccount
CORRESPONDANT_ARCHIVES	archiveDeposit/*
CORRESPONDANT_ARCHIVES	archiveManagement/*
CORRESPONDANT_ARCHIVES	destruction/*
CORRESPONDANT_ARCHIVES	journal/lifeCycleJournal
CORRESPONDANT_ARCHIVES	journal/searchLogArchive
\.


--
-- TOC entry 2601 (class 0 OID 536318)
-- Dependencies: 192
-- Data for Name: role; Type: TABLE DATA; Schema: auth; Owner: maarch
--

COPY auth.role ("roleId", "roleName", description, enabled) FROM stdin;
ADMIN	Administrateur technique	Groupe des administrateurs techniques du système	t
UTILISATEUR	Utilisateur	Groupe des utilisateurs, consultation et navigation	t
workflow_pod3au-0037-nz1f8t	Responsable d'activité	Groupe des responsables de service et des activités	t
workflow_pod3c1-1bc0-zh5adq	Producteur	Groupe des producteurs, versants	t
CORRESPONDANT_ARCHIVES	Correspondant d'archives	Groupe des archivistes / records managers / référents d'archives / administrateur fonctionnels	t
\.


--
-- TOC entry 2603 (class 0 OID 536341)
-- Dependencies: 194
-- Data for Name: roleMember; Type: TABLE DATA; Schema: auth; Owner: maarch
--

COPY auth."roleMember" ("roleId", "userAccountId") FROM stdin;
workflow_pod3c1-1bc0-zh5adq	aalambic
workflow_pod3c1-1bc0-zh5adq	bbain
workflow_pod3c1-1bc0-zh5adq	ccamus
workflow_pod3c1-1bc0-zh5adq	ddaull
workflow_pod3c1-1bc0-zh5adq	ddur
workflow_pod3c1-1bc0-zh5adq	ggrand
workflow_pod3c1-1bc0-zh5adq	hhier
workflow_pod3c1-1bc0-zh5adq	jjane
workflow_pod3c1-1bc0-zh5adq	jjonasz
workflow_pod3c1-1bc0-zh5adq	kkaar
workflow_pod3c1-1bc0-zh5adq	kkrach
workflow_pod3c1-1bc0-zh5adq	mmanfred
workflow_pod3c1-1bc0-zh5adq	ppacioli
workflow_pod3c1-1bc0-zh5adq	ppetit
workflow_pod3c1-1bc0-zh5adq	ppreboist
workflow_pod3c1-1bc0-zh5adq	rrenaud
workflow_pod3c1-1bc0-zh5adq	rreynolds
workflow_pod3c1-1bc0-zh5adq	ssaporta
workflow_pod3c1-1bc0-zh5adq	ssissoko
workflow_pod3c1-1bc0-zh5adq	sstar
workflow_pod3c1-1bc0-zh5adq	ttong
workflow_pod3c1-1bc0-zh5adq	ttule
workflow_pod3c1-1bc0-zh5adq	vvictoire
workflow_pod3c1-1bc0-zh5adq	workflow_pod5l0-232e-0aggqt
ADMIN	superadmin
workflow_pod3au-0037-nz1f8t	aackermann
workflow_pod3au-0037-nz1f8t	aadams
workflow_pod3au-0037-nz1f8t	aastier
UTILISATEUR	bboule
UTILISATEUR	cchaplin
UTILISATEUR	ccordy
workflow_pod3au-0037-nz1f8t	ccox
workflow_pod3au-0037-nz1f8t	eerina
workflow_pod3au-0037-nz1f8t	nnataly
UTILISATEUR	ppruvost
UTILISATEUR	sstallone
workflow_pod3au-0037-nz1f8t	sstone
CORRESPONDANT_ARCHIVES	bblier
CORRESPONDANT_ARCHIVES	ccharles
CORRESPONDANT_ARCHIVES	ddenis
\.


--
-- TOC entry 2605 (class 0 OID 536372)
-- Dependencies: 196
-- Data for Name: servicePrivilege; Type: TABLE DATA; Schema: auth; Owner: maarch
--

COPY auth."servicePrivilege" ("accountId", "serviceURI") FROM stdin;
SystemDepositor	recordsManagement/archive/createArchiveBatch
SystemDepositor	recordsManagement/archive/create
System	audit/event/createChainjournal
System	batchProcessing/scheduling/updateProcess
System	lifeCycle/journal/createChainjournal
System	recordsmanagement/archivecompliance/readperiodic
System	recordsManagement/archives/deleteDisposablearchives
System	recordsManagement/archives/updateArchivesretentionrule
System	recordsManagement/archives/updateIndexfulltext
maarchRM_pwop0t-2658-1s2whn	*
maarchRM_pwop1b-0ced-pmp8jy	*
\.


--
-- TOC entry 2607 (class 0 OID 536394)
-- Dependencies: 198
-- Data for Name: logScheduling; Type: TABLE DATA; Schema: batchProcessing; Owner: maarch
--

COPY "batchProcessing"."logScheduling" ("logId", "schedulingId", "executedBy", "launchedBy", "logDate", status, info) FROM stdin;
\.


--
-- TOC entry 2608 (class 0 OID 536403)
-- Dependencies: 199
-- Data for Name: notification; Type: TABLE DATA; Schema: batchProcessing; Owner: maarch
--

COPY "batchProcessing".notification ("notificationId", receivers, message, title, "createdDate", "createdBy", status, "sendDate", "sendBy") FROM stdin;
\.


--
-- TOC entry 2606 (class 0 OID 536386)
-- Dependencies: 197
-- Data for Name: scheduling; Type: TABLE DATA; Schema: batchProcessing; Owner: maarch
--

COPY "batchProcessing".scheduling ("schedulingId", name, "taskId", frequency, parameters, "executedBy", "lastExecution", "nextExecution", status) FROM stdin;
chainJournalAudit	Chaînage audit	01	00;20;;;;;;;	\N	System	2019-03-14 17:16:46.83441	2019-03-15 19:00:00	scheduled
chainJournalLifeCycle	Chaînage du journal du cycle de vie	02	00;20;;;;;;;	\N	System	2019-03-14 17:17:08.959422	2019-03-15 19:00:00	scheduled
deleteArchive	Destruction	04	00;19;;;;;;;	\N	System	2019-03-14 17:17:10.155329	2019-03-15 18:00:00	scheduled
integrity	Intégrité	03	00;01;;;;4;H;00;20	\N	System	2019-03-14 17:17:41.825506	2019-03-14 21:17:41.825513	scheduled
processDelivery	Traiter les communications	04	00;03;;;;;;;	\N	System	\N	\N	paused
processdestruction	Traiter les destructions	05	00;04;;;;;;;	\N	System	\N	\N	paused
processRestitution	Traiter les restitutions	06	00;05;;;;;;;	\N	System	\N	\N	paused
processTransfer	Traiter les transferts	07	00;06;;;;;;;	\N	System	\N	\N	paused
validateTransfer	Valider les transfert	08	00;07;;;;;;;	\N	System	\N	\N	paused
purge	Purge	09	00;08;;;;;;;	\N	System	\N	\N	paused
\.


--
-- TOC entry 2610 (class 0 OID 536422)
-- Dependencies: 201
-- Data for Name: address; Type: TABLE DATA; Schema: contact; Owner: maarch
--

COPY contact.address ("addressId", "contactId", purpose, room, floor, building, number, street, "postBox", block, "citySubDivision", "postCode", city, country) FROM stdin;
\.


--
-- TOC entry 2612 (class 0 OID 536447)
-- Dependencies: 203
-- Data for Name: communication; Type: TABLE DATA; Schema: contact; Owner: maarch
--

COPY contact.communication ("communicationId", "contactId", purpose, "comMeanCode", value, info) FROM stdin;
\.


--
-- TOC entry 2611 (class 0 OID 536437)
-- Dependencies: 202
-- Data for Name: communicationMean; Type: TABLE DATA; Schema: contact; Owner: maarch
--

COPY contact."communicationMean" (code, name, enabled) FROM stdin;
TE	Téléphone	t
AL	Téléphone mobile	t
FX	Fax	t
AO	URL	t
AU	FTP	t
EM	E-mail	t
AH	World Wide Web	f
\.


--
-- TOC entry 2609 (class 0 OID 536413)
-- Dependencies: 200
-- Data for Name: contact; Type: TABLE DATA; Schema: contact; Owner: maarch
--

COPY contact.contact ("contactId", "contactType", "orgName", "firstName", "lastName", title, function, service, "displayName") FROM stdin;
\.


--
-- TOC entry 2633 (class 0 OID 536741)
-- Dependencies: 224
-- Data for Name: address; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--

COPY "digitalResource".address ("resId", "repositoryId", path, "lastIntegrityCheck", "integrityCheckResult", packed, created) FROM stdin;
\.


--
-- TOC entry 2630 (class 0 OID 536698)
-- Dependencies: 221
-- Data for Name: cluster; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--

COPY "digitalResource".cluster ("clusterId", "clusterName", "clusterDescription") FROM stdin;
archives	Digital_resource_cluster_for_archives	Digital resource cluster for archives
\.


--
-- TOC entry 2634 (class 0 OID 536760)
-- Dependencies: 225
-- Data for Name: clusterRepository; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--

COPY "digitalResource"."clusterRepository" ("clusterId", "repositoryId", "writePriority", "readPriority", "deletePriority") FROM stdin;
archives	archives_1	1	1	1
archives	archives_2	1	2	2
\.


--
-- TOC entry 2637 (class 0 OID 536807)
-- Dependencies: 228
-- Data for Name: contentType; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--

COPY "digitalResource"."contentType" (name, mediatype, description, puids, "validationMode", "conversionMode", "textExtractionMode", "metadataExtractionMode") FROM stdin;
\.


--
-- TOC entry 2638 (class 0 OID 536815)
-- Dependencies: 229
-- Data for Name: conversionRule; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--

COPY "digitalResource"."conversionRule" ("conversionRuleId", puid, "conversionService", "targetPuid") FROM stdin;
workflow_pod75x-151b-v9jsef	fmt/412	dependency/fileSystem/plugins/libreOffice	fmt/95
workflow_pod763-1691-dli2t0	fmt/291	dependency/fileSystem/plugins/libreOffice	fmt/18
\.


--
-- TOC entry 2631 (class 0 OID 536706)
-- Dependencies: 222
-- Data for Name: digitalResource; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--

COPY "digitalResource"."digitalResource" ("archiveId", "resId", "clusterId", size, puid, mimetype, hash, "hashAlgorithm", "fileExtension", "fileName", "mediaInfo", created, updated, "relatedResId", "relationshipType") FROM stdin;
\.


--
-- TOC entry 2635 (class 0 OID 536778)
-- Dependencies: 226
-- Data for Name: package; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--

COPY "digitalResource".package ("packageId", method) FROM stdin;
\.


--
-- TOC entry 2636 (class 0 OID 536791)
-- Dependencies: 227
-- Data for Name: packedResource; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--

COPY "digitalResource"."packedResource" ("packageId", "resId", name) FROM stdin;
\.


--
-- TOC entry 2632 (class 0 OID 536729)
-- Dependencies: 223
-- Data for Name: repository; Type: TABLE DATA; Schema: digitalResource; Owner: maarch
--

COPY "digitalResource".repository ("repositoryId", "repositoryName", "repositoryReference", "repositoryType", "repositoryUri", parameters, "maxSize", enabled) FROM stdin;
archives_1	Digital resource repository for archives	repository_1	fileSystem	/var/www/laabs/data/maarchRM/repository/archives_1	\N	\N	t
archives_2	Digital resource repository for archives 2	repository_2	fileSystem	/var/www/laabs/data/maarchRM/repository/archives_2	\N	\N	t
\.


--
-- TOC entry 2639 (class 0 OID 536828)
-- Dependencies: 230
-- Data for Name: folder; Type: TABLE DATA; Schema: filePlan; Owner: maarch
--

COPY "filePlan".folder ("folderId", name, "parentFolderId", description, "ownerOrgRegNumber", closed) FROM stdin;
\.


--
-- TOC entry 2640 (class 0 OID 536843)
-- Dependencies: 231
-- Data for Name: position; Type: TABLE DATA; Schema: filePlan; Owner: maarch
--

COPY "filePlan"."position" ("folderId", "archiveId") FROM stdin;
\.


--
-- TOC entry 2641 (class 0 OID 536856)
-- Dependencies: 232
-- Data for Name: event; Type: TABLE DATA; Schema: lifeCycle; Owner: maarch
--

COPY "lifeCycle".event ("eventId", "eventType", "timestamp", "instanceName", "orgRegNumber", "orgUnitRegNumber", "accountId", "objectClass", "objectId", "operationResult", description, "eventInfo") FROM stdin;
\.


--
-- TOC entry 2642 (class 0 OID 536864)
-- Dependencies: 233
-- Data for Name: eventFormat; Type: TABLE DATA; Schema: lifeCycle; Owner: maarch
--

COPY "lifeCycle"."eventFormat" (type, format, message, notification) FROM stdin;
recordsManagement/accessRuleModification	resId hashAlgorithm hash address accessRuleStartDate accessRuleDuration previousAccessRuleStartDate previousAccessRuleDuration originatorOrgRegNumber archiverOrgRegNumber	Modification de la règle de communicabilité de l'archive %6$s	f
recordsManagement/addRelationship	resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId	Relation ajoutée avec l'archive %6$s	f
recordsManagement/archivalProfileModification	archivalProfileReference	Modification du profil %6$s.	f
recordsManagement/consultation	resId hash hashAlgorith address size	Consultation de la ressource %9$s	f
recordsManagement/conversion	resId hashAlgorithm hash address convertedResId convertedHashAlgorithm convertedHash convertedAddress software docId size	Conversion du document %18$s	f
recordsManagement/deleteRelationship	resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber relatedArchiveId	Relation avec l'archive %6$s supprimée	f
recordsManagement/delivery	resId hashAlgorithm hash address requesterOrgRegNumber archiverOrgRegNumber size	Communication de l'archive %6$s	f
recordsManagement/deposit	resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber format size	Dépôt de l'archive %6$s	f
recordsManagement/descriptionModification	property	Modification des métadonnées de l'archive %6$s.	f
recordsManagement/destructionRequest	resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size	Demande de destruction de l'archive %6$s	f
recordsManagement/destructionRequestCanceling	resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size	Annulation de la demande de destruction de l'archive %6$s	f
recordsManagement/destruction	resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size	Destruction de l'archive %6$s	f
recordsManagement/elimination	resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size	Élimination de l'archive %6$s	f
recordsManagement/freeze	resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber	Gel de l'archive %6$s	f
recordsManagement/integrityCheck	resId hash hashAlgorithm address requesterOrgRegNumber info	Validation d'intégrité	f
recordsManagement/metadataModification	resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber	Modification des métadonnées de l'archive %6$s	f
recordsManagement/profileCreation	archivalProfileReference	Création du profil %6$s	f
recordsManagement/profileDestruction	archivalProfileReference	Destruction du profil %6$s	f
recordsManagement/periodicIntegrityCheck	startDatetime endDatetime nbArchivesToCheck nbArchivesInSample archivesChecked	Validation périodique de l'intégrité	f
recordsManagement/restitution	resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber size	Restitution de l'archive %6$s	f
recordsManagement/retentionRuleModification	resId hashAlgorithm hash address retentionStartDate retentionDuration finalDisposition previousStartDate previousDuration previousFinalDisposition originatorOrgRegNumber archiverOrgRegNumber	Modification de la règle de conservation de l'archive %6$s	f
recordsManagement/unfreeze	resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber	Dégel de l'archive %6$s	f
recordsManagement/resourceDestruction	resId hashAlgorithm hash address originatorOrgRegNumber archiverOrgRegNumber	Destruction de la ressource %9$s	f
recordsManagement/depositNewResource	resId hashAlgorithm hash address originatorOrgRegNumber depositorOrgRegNumber archiverOrgRegNumber format size	Dépôt d'une ressource dans l'archive	f
medona/sending	type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference	Envoi du message %14$s de type %9$s de %11$s (%10$s) à %13$s (%12$s)	f
medona/reception	type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference	Réception du message %14$s de type %9$s de %11$s (%10$s) par %13$s (%12$s)	f
medona/validation	type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference code info	Validation du message %14$s : %16$s (%15$s)	f
medona/acknowledgement	type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference code info	Acquittement du message %14$s : %16$s (%15$s)	f
medona/processing	type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference	Traitement du message %14$s de type %9$s de %11$s (%10$s) par %13$s (%12$s)	f
medona/acceptance	type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference	Message %14$s de type %9$s accepté par %13$s (%12$s)	f
medona/rejection	type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference	Message %14$s de type %9$s rejeté par %13$s (%12$s)	f
medona/retry	type senderOrgRegNumber senderOrgName recipientOrgRegNumber recipientOrgName reference	Message %14$s de type %9$s réinitialisé par %13$s (%12$s)	f
organization/counting	orgName ownerOrgId	Compter le nombre d'objet numérique dans l'activité %6$s	f
organization/listing	orgName ownerOrgId	Lister les identifiants d'objet numérique de l'activité %6$s	f
organization/journal	orgName ownerOrgId	Lecture du journal de l'organisation %6$s	f
\.


--
-- TOC entry 2613 (class 0 OID 536468)
-- Dependencies: 204
-- Data for Name: archivalAgreement; Type: TABLE DATA; Schema: medona; Owner: maarch
--

COPY medona."archivalAgreement" ("archivalAgreementId", name, reference, description, "archivalProfileReference", "serviceLevelReference", "archiverOrgRegNumber", "depositorOrgRegNumber", "originatorOrgIds", "beginDate", "endDate", enabled, "allowedFormats", "maxSizeAgreement", "maxSizeTransfer", "maxSizeDay", "maxSizeWeek", "maxSizeMonth", "maxSizeYear", signed, "autoTransferAcceptance", "processSmallArchive") FROM stdin;
\.


--
-- TOC entry 2617 (class 0 OID 536516)
-- Dependencies: 208
-- Data for Name: controlAuthority; Type: TABLE DATA; Schema: medona; Owner: maarch
--

COPY medona."controlAuthority" ("originatorOrgUnitId", "controlAuthorityOrgUnitId") FROM stdin;
\.


--
-- TOC entry 2614 (class 0 OID 536478)
-- Dependencies: 205
-- Data for Name: message; Type: TABLE DATA; Schema: medona; Owner: maarch
--

COPY medona.message ("messageId", schema, type, status, date, reference, "accountId", "senderOrgRegNumber", "senderOrgName", "recipientOrgRegNumber", "recipientOrgName", "archivalAgreementReference", "replyCode", "operationDate", "receptionDate", "relatedReference", "requestReference", "replyReference", "authorizationReference", "authorizationReason", "authorizationRequesterOrgRegNumber", derogation, "dataObjectCount", size, data, path, active, archived, "isIncoming", comment) FROM stdin;
\.


--
-- TOC entry 2615 (class 0 OID 536488)
-- Dependencies: 206
-- Data for Name: messageComment; Type: TABLE DATA; Schema: medona; Owner: maarch
--

COPY medona."messageComment" ("messageId", comment, "commentId") FROM stdin;
\.


--
-- TOC entry 2616 (class 0 OID 536503)
-- Dependencies: 207
-- Data for Name: unitIdentifier; Type: TABLE DATA; Schema: medona; Owner: maarch
--

COPY medona."unitIdentifier" ("messageId", "objectClass", "objectId") FROM stdin;
\.


--
-- TOC entry 2648 (class 0 OID 536949)
-- Dependencies: 239
-- Data for Name: archivalProfileAccess; Type: TABLE DATA; Schema: organization; Owner: maarch
--

COPY organization."archivalProfileAccess" ("orgId", "archivalProfileReference", "originatorAccess", "serviceLevelReference", "userAccess") FROM stdin;
DAF	FACACH	f		{"subProfile": {}, "processingStatuses": {"QUALIFIED": {"actions": {"reject": {}, "redirect": {}, "validate": {}}}, "VALIDATED": {"actions": {"reject": {}, "approve": {}}}}}
DIP	DOSIP	t		\N
DSI	FACACH	f		{"subProfile": {}, "processingStatuses": {"QUALIFIED": {"actions": {"reject": {}, "redirect": {}, "validate": {}}}, "VALIDATED": {"actions": {"reject": {}, "approve": {}}}}}
FOUR	FACACH	t		{"history": {}, "subProfile": {}, "processingStatuses": {"NEW": {"actions": {"qualify": {}, "cancelQualify": {}}}, "APPROVED": {"actions": {"pay": {}, "updateMetadata": {}}}, "REJECTED": {"actions": {"cancelQualify": {}, "sendValidation": {}, "updateMetadata": {}, "sendToApprobation": {}}}, "MISQUALIFIED": {"actions": {"qualify": {}}}}}
FOUR	FACJU	t		\N
SALES	FACVEN	t		\N
SG	FACACH	f		{"subProfile": {}, "processingStatuses": {"QUALIFIED": {"actions": {"reject": {}, "redirect": {}, "validate": {}}}, "VALIDATED": {"actions": {"reject": {}, "approve": {}}}}}
PAIE	BULPAI	t		{"subProfile": {}, "processingStatuses": {}}
DAF	NOTSER	t		{"subProfile": {}, "processingStatuses": {}}
DCIAL	NOTSER	t		{"subProfile": {}, "processingStatuses": {}}
DSI	NOTSER	t		{"subProfile": {}, "processingStatuses": {}}
GIC	NOTSER	t		{"subProfile": {}, "processingStatuses": {}}
TENDER	PM	t		{"subProfile": {}, "processingStatuses": {}}
TENDER	COUNM	t		{"subProfile": {}, "processingStatuses": {}}
DOCSOC	FICCR	t		{"subProfile": {}, "processingStatuses": {}}
DOCSOC	LETC	t		{"subProfile": {}, "processingStatuses": {}}
DOCSOC	FICI	t		{"subProfile": {}, "processingStatuses": {}}
MARK	FACACH	f		{"subProfile": {}, "processingStatuses": {"QUALIFIED": {"actions": {"reject": {}, "redirect": {}, "validate": {}}}}}
DCIAL	FACACH	f		{"subProfile": {}, "processingStatuses": {"VALIDATED": {"actions": {"reject": {}, "approve": {}}}}}
RH	*	t	\N	\N
\.


--
-- TOC entry 2647 (class 0 OID 536936)
-- Dependencies: 238
-- Data for Name: orgContact; Type: TABLE DATA; Schema: organization; Owner: maarch
--

COPY organization."orgContact" ("contactId", "orgId", "isSelf") FROM stdin;
\.


--
-- TOC entry 2643 (class 0 OID 536880)
-- Dependencies: 234
-- Data for Name: orgType; Type: TABLE DATA; Schema: organization; Owner: maarch
--

COPY organization."orgType" (code, name) FROM stdin;
Collectivite	Collectivité
Societe	Société
Direction	Direction d'une entreprise ou d'une collectivité
Service	Service d'une entreprise ou d'une collectivité
Division	Division d'une entreprise
\.


--
-- TOC entry 2644 (class 0 OID 536888)
-- Dependencies: 235
-- Data for Name: organization; Type: TABLE DATA; Schema: organization; Owner: maarch
--

COPY organization.organization ("orgId", "orgName", "otherOrgName", "displayName", "registrationNumber", "beginDate", "endDate", "legalClassification", "businessType", description, "orgTypeCode", "orgRoleCodes", "taxIdentifier", "parentOrgId", "ownerOrgId", "isOrgUnit") FROM stdin;
ACME	Archives Conservation et Mémoire Électronique	\N	Archives Conservation et Mémoire Électronique	ACME	\N	\N	\N	\N	\N	\N	\N	\N	\N	\N	f
DAF	Direction Administrative et Financière	\N	Direction Administrative et Financière	DAF	\N	\N	\N	\N	\N	\N	\N	\N	ACME	ACME	t
SJ	Service Juridique	\N	Service Juridique	SJ	\N	\N	\N	\N	\N	\N	\N	\N	DAF	ACME	t
RH	Direction des Ressources Humaines	\N	Direction des Ressources Humaines	RH	\N	\N	\N	\N	\N	\N	\N	\N	ACME	ACME	t
GIC	Gestion et Conservation de l'Information	\N	Gestion et Conservation de l'Information	GIC	\N	\N	\N	\N	\N	\N	owner	\N	ACME	ACME	t
DG	Direction Générale	\N	Direction Générale	DG	\N	\N	\N	\N	\N	\N	\N	\N	DAF	ACME	t
SG	Services généraux	\N	Direction des Services Généraux	SG	\N	\N	\N	\N	\N	\N	\N	\N	DAF	ACME	t
DOCSOC	Documents de société	\N	Document de société	DOCSOC	\N	\N	\N	\N	\N	\N	\N	\N	DG	ACME	t
LITIGES	Suivi litiges Contentieux	\N	Suivi des litiges et contentieux	LITIGES	\N	\N	\N	\N	\N	\N	\N	\N	DG	ACME	t
ASSU	Assurances Groupe	\N	Assurances du Groupe	ASSU	\N	\N	\N	\N	\N	\N	\N	\N	SJ	ACME	t
DSI	Direction des SI	\N	Direction des Systèmes d'Information	DSI	\N	\N	\N	\N	\N	\N	\N	\N	ACME	ACME	t
DSOC	Droit des sociétés	\N	Droit des Sociétés	DSOC	\N	\N	\N	\N	\N	\N	\N	\N	SJ	ACME	t
CONCOM	Contrats Commerciaux	\N	Contrats Commerciaux	CONCOM	\N	\N	\N	\N	\N	\N	\N	\N	SJ	ACME	t
PAIE	Rémunération et Paie	\N	Rémunération et Paie	PAIE	\N	\N	\N	\N	\N	\N	\N	\N	RH	ACME	t
DIP	Dossiers du personnel	\N	Dossiers Individuels du Personnel	DIP	\N	\N	\N	\N	\N	\N	\N	\N	RH	ACME	t
NOTFRA	Notes Frais	\N	Gestion des Notes de Frais	NOTFRA	\N	\N	\N	\N	\N	\N	\N	\N	RH	ACME	t
MUT	Prévoyance/Mutuelle	\N	Prévoyance/Mutuelle	MUT	\N	\N	\N	\N	\N	\N	\N	\N	RH	ACME	t
CTBLE	Service Comptable	\N	Service Comptable	CTBLE	\N	\N	\N	\N	\N	\N	\N	\N	DAF	ACME	t
FOUR	Achats/Fournisseurs	\N	Comptabilité Achats/Fournisseurs	FOUR	\N	\N	\N	\N	\N	\N	\N	\N	CTBLE	ACME	t
SALES	Ventes/Clients	\N	Comptabilité Ventes/Clients	SALES	\N	\N	\N	\N	\N	\N	\N	\N	CTBLE	ACME	t
GEN	Comptabilité Générale	\N	Comptabilité Générale/Éditions comptables	GEN	\N	\N	\N	\N	\N	\N	\N	\N	CTBLE	ACME	t
TRESO	Trésorerie	\N	Trésorerie	TRESO	\N	\N	\N	\N	\N	\N	\N	\N	CTBLE	ACME	t
FISCA	Fiscalité	\N	Fiscalité	FISCA	\N	\N	\N	\N	\N	\N	\N	\N	CTBLE	ACME	t
ACHAT	Achats Groupe	\N	Achats Groupe	ACHAT	\N	\N	\N	\N	\N	\N	\N	\N	SG	ACME	t
SUPP	Support	\N	Support	SUPP	\N	\N	\N	\N	\N	\N	\N	\N	DSI	ACME	t
DCIAL	Direction Commerciale	\N	Direction Commerciale	DCIAL	\N	\N	\N	\N	\N	\N	\N	\N	ACME	ACME	t
CUST	Gestion Clients	\N	Gestion Clients	CUST	\N	\N	\N	\N	\N	\N	\N	\N	DCIAL	ACME	t
MARK	Marketing	\N	Marketing	MARK	\N	\N	\N	\N	\N	\N	\N	\N	DCIAL	ACME	t
DEAL	Offres Commerciales	\N	Offres Commerciales	DEAL	\N	\N	\N	\N	\N	\N	\N	\N	DCIAL	ACME	t
BILAN	Comptes de bilan	\N	Comptes de Bilan et Clôture d'Exercice	BILAN	\N	\N	\N	\N	\N	\N	\N	\N	CTBLE	ACME	t
AUTO	Gestion parc Auto	\N	Gestion du Parc Automobile	AUTO	\N	\N	\N	\N	\N	\N	\N	\N	SG	ACME	t
NETT	Nettoyage des Locaux	\N	Nettoyage des Locaux	NETT	\N	\N	\N	\N	\N	\N	\N	\N	SG	ACME	t
SOC	Charges Sociales	\N	Charges Sociales	SOC	\N	\N	\N	\N	\N	\N	\N	\N	RH	ACME	t
TENDER	Appels d'offres	\N	Réponses aux Appels d'Offres/Collectivités	TENDER	\N	\N	\N	\N	\N	\N	\N	\N	DCIAL	ACME	t
SIG	Gestion systèmes d'informations	\N	Gestion des Systèmes d'Information	SIG	\N	\N	\N	\N	\N	\N	\N	\N	DSI	ACME	t
SYSRES	Système et Réseaux	\N	Système et Réseaux	SYSRES	\N	\N	\N	\N	\N	\N	\N	\N	DSI	ACME	t
\.


--
-- TOC entry 2646 (class 0 OID 536928)
-- Dependencies: 237
-- Data for Name: servicePosition; Type: TABLE DATA; Schema: organization; Owner: maarch
--

COPY organization."servicePosition" ("serviceAccountId", "orgId") FROM stdin;
SystemDepositor	GIC
System	GIC
maarchRM_pwop1b-0ced-pmp8jy	RH
\.


--
-- TOC entry 2645 (class 0 OID 536915)
-- Dependencies: 236
-- Data for Name: userPosition; Type: TABLE DATA; Schema: organization; Owner: maarch
--

COPY organization."userPosition" ("userAccountId", "orgId", function, "default") FROM stdin;
ddaull	DAF	\N	t
jjonasz	LITIGES	\N	t
aackermann	FOUR	\N	t
aalambic	FOUR	\N	t
ccordy	FOUR	\N	t
ccamus	TRESO	\N	t
bboule	SALES	\N	t
ddur	SALES	\N	t
eerina	SALES	\N	t
ttong	SALES	\N	t
bbain	SJ	\N	t
rrenaud	SG	\N	t
ssaporta	AUTO	\N	t
ppacioli	NETT	\N	t
kkaar	TENDER	\N	t
ssissoko	CUST	\N	t
kkrach	MARK	\N	t
mmanfred	DEAL	\N	t
vvictoire	RH	\N	t
hhier	PAIE	\N	t
ppreboist	DSI	\N	t
ppruvost	DSI	\N	t
sstallone	SUPP	\N	t
sstar	SUPP	\N	t
ttule	SYSRES	\N	t
ddenis	GIC	\N	t
ccharles	GIC	\N	t
aadams	DAF	\N	t
aastier	SG	\N	t
workflow_pod5l0-232e-0aggqt	GEN	\N	t
bblier	GIC	\N	t
cchaplin	RH	\N	t
ccox	SJ	\N	t
ggrand	RH	\N	t
jjane	DOCSOC	\N	t
nnataly	DSI	\N	t
ppetit	CTBLE	\N	t
rreynolds	ACHAT	\N	t
sstone	DCIAL	\N	t
\.


--
-- TOC entry 2618 (class 0 OID 536530)
-- Dependencies: 209
-- Data for Name: accessRule; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

COPY "recordsManagement"."accessRule" (code, duration, description) FROM stdin;
\.


--
-- TOC entry 2620 (class 0 OID 536546)
-- Dependencies: 211
-- Data for Name: archivalProfile; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

COPY "recordsManagement"."archivalProfile" ("archivalProfileId", reference, name, "descriptionSchema", "descriptionClass", "retentionStartDate", "retentionRuleCode", description, "accessRuleCode", "acceptUserIndex", "acceptArchiveWithoutProfile", "fileplanLevel", "processingStatuses") FROM stdin;
1	COUA	Courrier Administratif	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
2	PRVN	Procès-Verbal de Négociation	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
3	PRVIF	Procès-verbal à Incidence Financière	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
4	ETAR	État de Rapprochement	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
5	RELCC	Relevé de Contrôle de Caisse	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
6	CTRF	Contrat Fournisseur	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
7	DCLTVA	Déclaration de TVA	\N	\N	originatingDate	IMP	\N	\N	t	t	file	\N
8	QUTP	Quittance de Paiement	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
9	FICIC	Fiche d'Imputation Comptable	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
10	FACJU	Facture Justificative	\N	\N	originatingDate	COM	\N	\N	t	t	item	\N
11	FICREC	Fiche Récapitulative	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
12	DOSC	Dossiers Caisse	\N	\N	originatingDate	\N	\N	\N	t	t	file	\N
13	PIECD	Pièce de Caisse-Dépense	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
14	PIEJ	Pièce Justificative	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
15	DOSB	Dossiers Banque	\N	\N	originatingDate	\N	\N	\N	t	t	file	\N
16	PIEBD	Pièce de Banque-Dépense	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
17	FICDG	Fiche DG	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
18	NOTSER	Note de service	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
19	PM	Passation de marché	\N	\N	originatingDate	\N	\N	\N	t	t	file	\N
20	BORT	Bordereau de transmission	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
21	COUNM	Courrier de notification de marché	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
22	PRVA	Procès-Verbal d'Attribution	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
23	PRVOP	Proces-Verbal d'Ouverture des Plis	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
24	RAPEO	Rapport d’Évaluation des Offres	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
26	DEMC	Demande de Cotation	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
27	RAPFOR	Rapport de Formation	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
28	DOSIP	Dossier Individuel du Personnel	\N	\N	originatingDate	DIP	\N	\N	t	t	file	\N
29	ETAC	Etat Civil	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
30	CURV	Curriculum Vitae	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
31	EXTAN	Extrait d'Acte de Naissance	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
33	CASJU	Casier Judiciaire	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
34	ATTSU	Attestation de succès	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
35	CTRTRV	Contrat de Travail	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
37	ATTT	Attestation de Travail	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
39	CNSS	Caisse Nationale de Sécurité Sociale	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
41	CAR	Carrière	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
42	DECIN	Décision de nomination	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
43	DECIR	Décision de redéploiement	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
47	ATTF	Attestation de formation	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
53	COURRN	Courrier Répartition du Résultat Net	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
54	COUDS	Courrier Domiciliation de Salaire	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
55	FICP	Fiche de Poste	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
56	FICF	Fiche de Fonction	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
57	DEMA	Demandes Administratives	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
58	COUAA	Courrier Autorisation d'Absence	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
59	COUCA	Courrier Congés Administratifs	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
60	COUDE	Courrier Demande d'Emploi	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
61	DOSETU	Dossier de Synthèse et d’Étude	\N	\N	originatingDate	\N	\N	\N	t	t	file	\N
62	FICRM	Fiche de Remontée Mensuelle	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
63	RAPT	Rapport Trimestriel	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
64	RAPMOE	Rapport de Mise en Œuvre	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
65	RAPA	Rapport d'Activité	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
66	RAPSE	Rapport de Suivi et Évaluation	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
67	RAPGES	Rapport de Gestion	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
68	TDRE	Termes de Références des Études	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
69	DCRN	Décret de Nomination	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
70	FICCR	Fiche de compte rendu	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
71	LETC	Lettre circulaire	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
72	FICI	Fiche d'instruction	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
73	RAPAMI	Rapport d'Audit et Missions Internes	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
74	RAPAE	Rapport d'Audit Externe	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
75	RAPER	Rapport d’Études et Recherches	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
76	COUABID	Courrier Arrivée BID	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
77	SMIROP	Fiche de visite SMIROP	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
78	VISA	Visas obtenus	\N	\N	originatingDate	\N	\N	\N	t	t	item	\N
79	FACVEN	Facture de vente	\N	\N	originatingDate	COM	\N	\N	t	t	item	\N
workflow_pod6si-219d-hiploo	BULPAI	Bulletins de paie	\N		\N	BULPAI		\N	f	f	item	{}
80	FACACH	Facture d'achat	\N		originatingDate	COM		\N	t	f	item	{"NEW": {"type": "initial", "label": "Nouvelle(s) facture(s)", "actions": {"qualify": {}, "cancelQualify": {}}, "default": true, "position": 0, "filterUserAccess": false}, "PAYED": {"type": "final", "label": "Payée", "actions": {}, "default": false, "position": 6, "filterUserAccess": false}, "APPROVED": {"type": "intermediate", "label": "À payer", "actions": {"pay": {}, "updateMetadata": {}}, "default": false, "position": 5, "filterUserAccess": true}, "REJECTED": {"type": "intermediate", "label": "Rejetée(s)", "actions": {"sendValidation": {}, "updateMetadata": {}, "sendToApprobation": {}}, "default": false, "position": 3, "filterUserAccess": true}, "CANCELLED": {"type": "final", "label": "Annulée", "actions": {}, "default": false, "position": 7, "filterUserAccess": false}, "QUALIFIED": {"type": "intermediate", "label": "À valider", "actions": {"reject": {}, "redirect": {}, "validate": {}}, "default": false, "position": 1, "filterUserAccess": true}, "VALIDATED": {"type": "intermediate", "label": "À approuver", "actions": {"reject": {}, "approve": {}}, "default": false, "position": 4, "filterUserAccess": true}, "MISQUALIFIED": {"type": "intermediate", "label": "À requalifier", "actions": {"qualify": {}}, "default": false, "position": 2, "filterUserAccess": true}}
\.


--
-- TOC entry 2621 (class 0 OID 536568)
-- Dependencies: 212
-- Data for Name: archivalProfileContents; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

COPY "recordsManagement"."archivalProfileContents" ("parentProfileId", "containedProfileId") FROM stdin;
7	8
7	9
7	10
7	11
12	9
12	13
12	14
15	9
15	14
15	16
19	2
19	18
19	20
19	21
19	22
19	23
19	24
19	26
28	1
28	29
28	30
28	31
28	33
28	34
28	35
28	37
28	39
28	41
28	42
28	43
28	47
28	53
28	54
28	55
28	56
28	57
28	58
28	59
28	60
28	77
28	78
61	1
61	17
61	63
61	64
61	65
61	66
61	67
61	68
61	69
\.


--
-- TOC entry 2625 (class 0 OID 536621)
-- Dependencies: 216
-- Data for Name: archive; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

COPY "recordsManagement".archive ("archiveId", "originatorArchiveId", "depositorArchiveId", "archiverArchiveId", "archiveName", "storagePath", "filePlanPosition", "fileplanLevel", "originatingDate", "descriptionClass", description, text, "originatorOrgRegNumber", "originatorOwnerOrgId", "originatorOwnerOrgRegNumber", "depositorOrgRegNumber", "archiverOrgRegNumber", "userOrgRegNumbers", "archivalProfileReference", "archivalAgreementReference", "serviceLevelReference", "retentionRuleCode", "retentionStartDate", "retentionDuration", "finalDisposition", "disposalDate", "retentionRuleStatus", "accessRuleCode", "accessRuleDuration", "accessRuleStartDate", "accessRuleComDate", "storageRuleCode", "storageRuleDuration", "storageRuleStartDate", "storageRuleEndDate", "classificationRuleCode", "classificationRuleDuration", "classificationRuleStartDate", "classificationEndDate", "classificationLevel", "classificationOwner", "depositDate", "lastCheckDate", "lastDeliveryDate", "lastModificationDate", status, "processingStatus", "parentArchiveId", "fullTextIndexation") FROM stdin;
\.


--
-- TOC entry 2623 (class 0 OID 536595)
-- Dependencies: 214
-- Data for Name: archiveDescription; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

COPY "recordsManagement"."archiveDescription" ("archivalProfileId", "fieldName", required, "position", "isImmutable", "isRetained", "isInList") FROM stdin;
1	org	f	0	f	f	f
2	org	f	0	f	f	f
3	org	f	0	f	f	f
4	org	f	0	f	f	f
5	org	f	0	f	f	f
6	org	f	0	f	f	f
28	fullname	f	1	f	f	f
28	empid	t	2	f	f	f
61	service	f	0	f	f	f
69	service	f	0	f	f	f
70	service	f	0	f	f	f
71	service	f	0	f	f	f
72	service	f	0	f	f	f
73	service	f	0	f	f	f
74	service	f	0	f	f	f
75	service	f	0	f	f	f
76	service	f	0	f	f	f
79	customer	f	0	f	f	f
79	salesPerson	f	0	f	f	f
workflow_pod6si-219d-hiploo	empid	t	0	f	\N	f
workflow_pod6si-219d-hiploo	fullname	t	1	f	\N	f
workflow_pod6si-219d-hiploo	service	f	2	f	\N	f
workflow_pod6si-219d-hiploo	org	t	3	f	\N	f
80	service	f	0	f	\N	t
80	taxIdentifier	t	1	f	\N	f
80	supplier	f	2	f	\N	t
80	netPayable	f	3	f	\N	t
80	dueDate	f	4	f	\N	f
80	orderNumber	f	5	f	\N	f
\.


--
-- TOC entry 2626 (class 0 OID 536646)
-- Dependencies: 217
-- Data for Name: archiveRelationship; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

COPY "recordsManagement"."archiveRelationship" ("archiveId", "relatedArchiveId", "typeCode", description) FROM stdin;
\.


--
-- TOC entry 2628 (class 0 OID 536672)
-- Dependencies: 219
-- Data for Name: descriptionClass; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

COPY "recordsManagement"."descriptionClass" (name, label) FROM stdin;
\.


--
-- TOC entry 2622 (class 0 OID 536586)
-- Dependencies: 213
-- Data for Name: descriptionField; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

COPY "recordsManagement"."descriptionField" (name, label, type, "default", "minLength", "maxLength", "minValue", "maxValue", enumeration, facets, pattern, "isArray") FROM stdin;
org	Organisation	name		\N	\N	\N	\N	["ACME Paris","ACME Dakar","ACME Cotonou"]	\N	\N	f
fullname	Nom complet	name	\N	\N	\N	\N	\N	\N	\N	\N	f
empid	Matricule	name	\N	\N	\N	\N	\N	\N	\N	\N	f
customer	Client	text	\N	\N	\N	\N	\N	\N	\N	\N	f
salesPerson	Vendeur	text	\N	\N	\N	\N	\N	\N	\N	\N	f
documentId	Identifiant de document	name	\N	\N	\N	\N	\N	\N	\N	\N	f
taxIdentifier	N° TVA Intraco.	name	\N	\N	\N	\N	\N	\N	\N	\N	f
supplier	Fournisseur	text	\N	\N	\N	\N	\N	\N	\N	\N	f
netPayable	Net à payer	number	\N	\N	\N	\N	\N	\N	\N	\N	f
dueDate	Date d'échéance	date	\N	\N	\N	\N	\N	\N	\N	\N	f
orderNumber	Numéro de commande	name	\N	\N	\N	\N	\N	\N	\N	\N	f
service	Service Concerné	name		\N	\N	\N	\N	["MARK","DSG","DSI","DAF"]	\N	\N	f
\.


--
-- TOC entry 2627 (class 0 OID 536664)
-- Dependencies: 218
-- Data for Name: log; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

COPY "recordsManagement".log ("archiveId", "fromDate", "toDate", "processId", "processName", type, "ownerOrgRegNumber") FROM stdin;
\.


--
-- TOC entry 2619 (class 0 OID 536538)
-- Dependencies: 210
-- Data for Name: retentionRule; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

COPY "recordsManagement"."retentionRule" (code, duration, "finalDisposition", description, label, "implementationDate") FROM stdin;
BULPAI	P5Y	destruction	Code du Travail, art. L3243-4 - Code de la Sécurité Sociale, art. L243-12	Bulletins de paie	\N
DIP	P90Y	destruction	Convention Collective nationale de retraite et de prévoyance des cadres, art. 23	Dossier individuel du personnel	\N
COM	P10Y	destruction	Code du commerce, Article L123-22	Documents comptables	\N
IMP	P6Y	destruction	Livre des Procédures Fiscales, art 102 B et L 169 : Livres, registres, documents ou pièces sur lesquels peuvent s'exercer les droits de communication, d'enquête et de contrôle de l'administration	Contrôle de l'impôt	\N
IMPS	P10Y	destruction	Livre des Procédures Fiscales, art 102 B et L 169 alinea 2: Les registres tenus en application du 9 de l'article 298 sexdecies F du code général des impôts et du 5 de l'article 298 sexdecies G du même code	Impôt sur les sociétés et liasses fiscales	\N
IMPA	P3Y	destruction	Livre des Procédures Fiscales, art 102 B et L 169 alinea 3	Taxe professionnelle	\N
GES	P5Y	destruction	Documents de gestion	Documents de gestion	\N
\.


--
-- TOC entry 2624 (class 0 OID 536611)
-- Dependencies: 215
-- Data for Name: serviceLevel; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

COPY "recordsManagement"."serviceLevel" ("serviceLevelId", reference, "digitalResourceClusterId", control, "default", "samplingFrequency", "samplingRate") FROM stdin;
ServiceLevel_001	serviceLevel_001	archives	formatDetection formatValidation virusCheck convertOnDeposit	f	2	50
ServiceLevel_002	serviceLevel_002	archives		t	2	50
\.


--
-- TOC entry 2629 (class 0 OID 536680)
-- Dependencies: 220
-- Data for Name: storageRule; Type: TABLE DATA; Schema: recordsManagement; Owner: maarch
--

COPY "recordsManagement"."storageRule" (code, duration, description, label) FROM stdin;
\.


--
-- TOC entry 2297 (class 2606 OID 536314)
-- Name: event event_pkey; Type: CONSTRAINT; Schema: audit; Owner: maarch
--

ALTER TABLE ONLY audit.event
    ADD CONSTRAINT event_pkey PRIMARY KEY ("eventId");


--
-- TOC entry 2301 (class 2606 OID 536340)
-- Name: account account_accountName_key; Type: CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth.account
    ADD CONSTRAINT "account_accountName_key" UNIQUE ("accountName");


--
-- TOC entry 2303 (class 2606 OID 536338)
-- Name: account account_pkey; Type: CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth.account
    ADD CONSTRAINT account_pkey PRIMARY KEY ("accountId");


--
-- TOC entry 2307 (class 2606 OID 536366)
-- Name: privilege privilege_roleId_userStory_key; Type: CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth.privilege
    ADD CONSTRAINT "privilege_roleId_userStory_key" UNIQUE ("roleId", "userStory");


--
-- TOC entry 2305 (class 2606 OID 536348)
-- Name: roleMember roleMember_roleId_userAccountId_key; Type: CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth."roleMember"
    ADD CONSTRAINT "roleMember_roleId_userAccountId_key" UNIQUE ("roleId", "userAccountId");


--
-- TOC entry 2299 (class 2606 OID 536326)
-- Name: role role_pkey; Type: CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth.role
    ADD CONSTRAINT role_pkey PRIMARY KEY ("roleId");


--
-- TOC entry 2309 (class 2606 OID 536379)
-- Name: servicePrivilege servicePrivilege_accountId_serviceURI_key; Type: CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth."servicePrivilege"
    ADD CONSTRAINT "servicePrivilege_accountId_serviceURI_key" UNIQUE ("accountId", "serviceURI");


--
-- TOC entry 2314 (class 2606 OID 536402)
-- Name: logScheduling logScheduling_pkey; Type: CONSTRAINT; Schema: batchProcessing; Owner: maarch
--

ALTER TABLE ONLY "batchProcessing"."logScheduling"
    ADD CONSTRAINT "logScheduling_pkey" PRIMARY KEY ("logId");


--
-- TOC entry 2316 (class 2606 OID 536410)
-- Name: notification notification_pkey; Type: CONSTRAINT; Schema: batchProcessing; Owner: maarch
--

ALTER TABLE ONLY "batchProcessing".notification
    ADD CONSTRAINT notification_pkey PRIMARY KEY ("notificationId");


--
-- TOC entry 2311 (class 2606 OID 536393)
-- Name: scheduling scheduling_pkey; Type: CONSTRAINT; Schema: batchProcessing; Owner: maarch
--

ALTER TABLE ONLY "batchProcessing".scheduling
    ADD CONSTRAINT scheduling_pkey PRIMARY KEY ("schedulingId");


--
-- TOC entry 2320 (class 2606 OID 536431)
-- Name: address address_contactId_purpose_key; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.address
    ADD CONSTRAINT "address_contactId_purpose_key" UNIQUE ("contactId", purpose);


--
-- TOC entry 2322 (class 2606 OID 536429)
-- Name: address address_pkey; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.address
    ADD CONSTRAINT address_pkey PRIMARY KEY ("addressId");


--
-- TOC entry 2324 (class 2606 OID 536446)
-- Name: communicationMean communicationMean_name_key; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact."communicationMean"
    ADD CONSTRAINT "communicationMean_name_key" UNIQUE (name);


--
-- TOC entry 2326 (class 2606 OID 536444)
-- Name: communicationMean communicationMean_pkey; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact."communicationMean"
    ADD CONSTRAINT "communicationMean_pkey" PRIMARY KEY (code);


--
-- TOC entry 2328 (class 2606 OID 536456)
-- Name: communication communication_contactId_purpose_comMeanCode_key; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.communication
    ADD CONSTRAINT "communication_contactId_purpose_comMeanCode_key" UNIQUE ("contactId", purpose, "comMeanCode");


--
-- TOC entry 2330 (class 2606 OID 536454)
-- Name: communication communication_pkey; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.communication
    ADD CONSTRAINT communication_pkey PRIMARY KEY ("communicationId");


--
-- TOC entry 2318 (class 2606 OID 536421)
-- Name: contact contact_pkey; Type: CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.contact
    ADD CONSTRAINT contact_pkey PRIMARY KEY ("contactId");


--
-- TOC entry 2408 (class 2606 OID 536749)
-- Name: address address_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".address
    ADD CONSTRAINT address_pkey PRIMARY KEY ("resId", "repositoryId");


--
-- TOC entry 2410 (class 2606 OID 536767)
-- Name: clusterRepository clusterRepository_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."clusterRepository"
    ADD CONSTRAINT "clusterRepository_pkey" PRIMARY KEY ("clusterId", "repositoryId");


--
-- TOC entry 2396 (class 2606 OID 536705)
-- Name: cluster cluster_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".cluster
    ADD CONSTRAINT cluster_pkey PRIMARY KEY ("clusterId");


--
-- TOC entry 2414 (class 2606 OID 536814)
-- Name: contentType contentType_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."contentType"
    ADD CONSTRAINT "contentType_pkey" PRIMARY KEY (name);


--
-- TOC entry 2416 (class 2606 OID 536822)
-- Name: conversionRule conversionRule_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."conversionRule"
    ADD CONSTRAINT "conversionRule_pkey" PRIMARY KEY ("conversionRuleId");


--
-- TOC entry 2418 (class 2606 OID 536824)
-- Name: conversionRule conversionRule_puid_key; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."conversionRule"
    ADD CONSTRAINT "conversionRule_puid_key" UNIQUE (puid);


--
-- TOC entry 2400 (class 2606 OID 536713)
-- Name: digitalResource digitalResource_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."digitalResource"
    ADD CONSTRAINT "digitalResource_pkey" PRIMARY KEY ("resId");


--
-- TOC entry 2412 (class 2606 OID 536785)
-- Name: package package_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".package
    ADD CONSTRAINT package_pkey PRIMARY KEY ("packageId");


--
-- TOC entry 2402 (class 2606 OID 536736)
-- Name: repository repository_pkey; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".repository
    ADD CONSTRAINT repository_pkey PRIMARY KEY ("repositoryId");


--
-- TOC entry 2404 (class 2606 OID 536738)
-- Name: repository repository_repositoryReference_key; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".repository
    ADD CONSTRAINT "repository_repositoryReference_key" UNIQUE ("repositoryReference");


--
-- TOC entry 2406 (class 2606 OID 536740)
-- Name: repository repository_repositoryUri_key; Type: CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".repository
    ADD CONSTRAINT "repository_repositoryUri_key" UNIQUE ("repositoryUri");


--
-- TOC entry 2421 (class 2606 OID 536837)
-- Name: folder filePlan_name_parentFolderId_key; Type: CONSTRAINT; Schema: filePlan; Owner: maarch
--

ALTER TABLE ONLY "filePlan".folder
    ADD CONSTRAINT "filePlan_name_parentFolderId_key" UNIQUE (name, "parentFolderId");


--
-- TOC entry 2423 (class 2606 OID 536835)
-- Name: folder folder_pkey; Type: CONSTRAINT; Schema: filePlan; Owner: maarch
--

ALTER TABLE ONLY "filePlan".folder
    ADD CONSTRAINT folder_pkey PRIMARY KEY ("folderId");


--
-- TOC entry 2433 (class 2606 OID 536872)
-- Name: eventFormat eventFormat_pkey; Type: CONSTRAINT; Schema: lifeCycle; Owner: maarch
--

ALTER TABLE ONLY "lifeCycle"."eventFormat"
    ADD CONSTRAINT "eventFormat_pkey" PRIMARY KEY (type);


--
-- TOC entry 2426 (class 2606 OID 536863)
-- Name: event event_pkey; Type: CONSTRAINT; Schema: lifeCycle; Owner: maarch
--

ALTER TABLE ONLY "lifeCycle".event
    ADD CONSTRAINT event_pkey PRIMARY KEY ("eventId");


--
-- TOC entry 2332 (class 2606 OID 536475)
-- Name: archivalAgreement archivalAgreement_pkey; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."archivalAgreement"
    ADD CONSTRAINT "archivalAgreement_pkey" PRIMARY KEY ("archivalAgreementId");


--
-- TOC entry 2334 (class 2606 OID 536477)
-- Name: archivalAgreement archivalAgreement_reference_key; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."archivalAgreement"
    ADD CONSTRAINT "archivalAgreement_reference_key" UNIQUE (reference);


--
-- TOC entry 2351 (class 2606 OID 536523)
-- Name: controlAuthority controlAuthority_pkey; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."controlAuthority"
    ADD CONSTRAINT "controlAuthority_pkey" PRIMARY KEY ("originatorOrgUnitId");


--
-- TOC entry 2343 (class 2606 OID 536497)
-- Name: messageComment messageComment_messageId_comment_key; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."messageComment"
    ADD CONSTRAINT "messageComment_messageId_comment_key" UNIQUE ("messageId", comment);


--
-- TOC entry 2345 (class 2606 OID 536495)
-- Name: messageComment messageComment_pkey; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."messageComment"
    ADD CONSTRAINT "messageComment_pkey" PRIMARY KEY ("commentId");


--
-- TOC entry 2339 (class 2606 OID 536485)
-- Name: message message_pkey; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona.message
    ADD CONSTRAINT message_pkey PRIMARY KEY ("messageId");


--
-- TOC entry 2341 (class 2606 OID 536487)
-- Name: message message_type_reference_senderOrgRegNumber_key; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona.message
    ADD CONSTRAINT "message_type_reference_senderOrgRegNumber_key" UNIQUE (type, reference, "senderOrgRegNumber");


--
-- TOC entry 2349 (class 2606 OID 536510)
-- Name: unitIdentifier unitIdentifier_messageId_objectClass_objectId_key; Type: CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."unitIdentifier"
    ADD CONSTRAINT "unitIdentifier_messageId_objectClass_objectId_key" UNIQUE ("messageId", "objectClass", "objectId");


--
-- TOC entry 2449 (class 2606 OID 536957)
-- Name: archivalProfileAccess archivalProfileAccess_pkey; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."archivalProfileAccess"
    ADD CONSTRAINT "archivalProfileAccess_pkey" PRIMARY KEY ("orgId", "archivalProfileReference");


--
-- TOC entry 2447 (class 2606 OID 536943)
-- Name: orgContact orgContact_pkey; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."orgContact"
    ADD CONSTRAINT "orgContact_pkey" PRIMARY KEY ("contactId", "orgId");


--
-- TOC entry 2435 (class 2606 OID 536887)
-- Name: orgType orgType_pkey; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."orgType"
    ADD CONSTRAINT "orgType_pkey" PRIMARY KEY (code);


--
-- TOC entry 2437 (class 2606 OID 536895)
-- Name: organization organization_pkey; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization.organization
    ADD CONSTRAINT organization_pkey PRIMARY KEY ("orgId");


--
-- TOC entry 2439 (class 2606 OID 536897)
-- Name: organization organization_registrationNumber_key; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization.organization
    ADD CONSTRAINT "organization_registrationNumber_key" UNIQUE ("registrationNumber");


--
-- TOC entry 2441 (class 2606 OID 536899)
-- Name: organization organization_taxIdentifier_key; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization.organization
    ADD CONSTRAINT "organization_taxIdentifier_key" UNIQUE ("taxIdentifier");


--
-- TOC entry 2445 (class 2606 OID 536935)
-- Name: servicePosition servicePosition_pkey; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."servicePosition"
    ADD CONSTRAINT "servicePosition_pkey" PRIMARY KEY ("serviceAccountId", "orgId");


--
-- TOC entry 2443 (class 2606 OID 536922)
-- Name: userPosition userPosition_pkey; Type: CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."userPosition"
    ADD CONSTRAINT "userPosition_pkey" PRIMARY KEY ("userAccountId", "orgId");


--
-- TOC entry 2353 (class 2606 OID 536537)
-- Name: accessRule accessRule_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."accessRule"
    ADD CONSTRAINT "accessRule_pkey" PRIMARY KEY (code);


--
-- TOC entry 2361 (class 2606 OID 536575)
-- Name: archivalProfileContents archivalProfileContents_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfileContents"
    ADD CONSTRAINT "archivalProfileContents_pkey" PRIMARY KEY ("parentProfileId", "containedProfileId");


--
-- TOC entry 2357 (class 2606 OID 536555)
-- Name: archivalProfile archivalProfile_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfile"
    ADD CONSTRAINT "archivalProfile_pkey" PRIMARY KEY ("archivalProfileId");


--
-- TOC entry 2359 (class 2606 OID 536557)
-- Name: archivalProfile archivalProfile_reference_key; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfile"
    ADD CONSTRAINT "archivalProfile_reference_key" UNIQUE (reference);


--
-- TOC entry 2365 (class 2606 OID 536605)
-- Name: archiveDescription archiveDescription_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archiveDescription"
    ADD CONSTRAINT "archiveDescription_pkey" PRIMARY KEY ("archivalProfileId", "fieldName");


--
-- TOC entry 2386 (class 2606 OID 536653)
-- Name: archiveRelationship archiveRelationship_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archiveRelationship"
    ADD CONSTRAINT "archiveRelationship_pkey" PRIMARY KEY ("archiveId", "relatedArchiveId", "typeCode");


--
-- TOC entry 2375 (class 2606 OID 536629)
-- Name: archive archive_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement".archive
    ADD CONSTRAINT archive_pkey PRIMARY KEY ("archiveId");


--
-- TOC entry 2392 (class 2606 OID 536679)
-- Name: descriptionClass descriptionClass_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."descriptionClass"
    ADD CONSTRAINT "descriptionClass_pkey" PRIMARY KEY (name);


--
-- TOC entry 2363 (class 2606 OID 536594)
-- Name: descriptionField descriptionField_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."descriptionField"
    ADD CONSTRAINT "descriptionField_pkey" PRIMARY KEY (name);


--
-- TOC entry 2390 (class 2606 OID 536671)
-- Name: log log_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement".log
    ADD CONSTRAINT log_pkey PRIMARY KEY ("archiveId");


--
-- TOC entry 2355 (class 2606 OID 536545)
-- Name: retentionRule retentionRule_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."retentionRule"
    ADD CONSTRAINT "retentionRule_pkey" PRIMARY KEY (code);


--
-- TOC entry 2367 (class 2606 OID 536618)
-- Name: serviceLevel serviceLevel_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."serviceLevel"
    ADD CONSTRAINT "serviceLevel_pkey" PRIMARY KEY ("serviceLevelId");


--
-- TOC entry 2369 (class 2606 OID 536620)
-- Name: serviceLevel serviceLevel_reference_key; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."serviceLevel"
    ADD CONSTRAINT "serviceLevel_reference_key" UNIQUE (reference);


--
-- TOC entry 2394 (class 2606 OID 536687)
-- Name: storageRule storageRule_pkey; Type: CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."storageRule"
    ADD CONSTRAINT "storageRule_pkey" PRIMARY KEY (code);


--
-- TOC entry 2294 (class 1259 OID 536316)
-- Name: audit_event_eventDate_idx; Type: INDEX; Schema: audit; Owner: maarch
--

CREATE INDEX "audit_event_eventDate_idx" ON audit.event USING btree ("eventDate");


--
-- TOC entry 2295 (class 1259 OID 536315)
-- Name: audit_event_instanceName_idx; Type: INDEX; Schema: audit; Owner: maarch
--

CREATE INDEX "audit_event_instanceName_idx" ON audit.event USING btree ("instanceName");


--
-- TOC entry 2312 (class 1259 OID 536411)
-- Name: batchProcessing_logScheduling_schedulingId_idx; Type: INDEX; Schema: batchProcessing; Owner: maarch
--

CREATE INDEX "batchProcessing_logScheduling_schedulingId_idx" ON "batchProcessing"."logScheduling" USING btree ("schedulingId");


--
-- TOC entry 2397 (class 1259 OID 536825)
-- Name: digitalResource_digitalResource_archiveId_idx; Type: INDEX; Schema: digitalResource; Owner: maarch
--

CREATE INDEX "digitalResource_digitalResource_archiveId_idx" ON "digitalResource"."digitalResource" USING btree ("archiveId");


--
-- TOC entry 2398 (class 1259 OID 536826)
-- Name: digitalResource_digitalResource_relatedResId__relationshipType_; Type: INDEX; Schema: digitalResource; Owner: maarch
--

CREATE INDEX "digitalResource_digitalResource_relatedResId__relationshipType_" ON "digitalResource"."digitalResource" USING btree ("relatedResId", "relationshipType");


--
-- TOC entry 2419 (class 1259 OID 536854)
-- Name: filePlan_folder_ownerOrgRegNumber_idx; Type: INDEX; Schema: filePlan; Owner: maarch
--

CREATE INDEX "filePlan_folder_ownerOrgRegNumber_idx" ON "filePlan".folder USING btree ("ownerOrgRegNumber");


--
-- TOC entry 2424 (class 1259 OID 536873)
-- Name: event_objectId_idx; Type: INDEX; Schema: lifeCycle; Owner: maarch
--

CREATE INDEX "event_objectId_idx" ON "lifeCycle".event USING btree ("objectId");


--
-- TOC entry 2427 (class 1259 OID 536874)
-- Name: event_timestamp_idx; Type: INDEX; Schema: lifeCycle; Owner: maarch
--

CREATE INDEX event_timestamp_idx ON "lifeCycle".event USING btree ("timestamp");


--
-- TOC entry 2428 (class 1259 OID 536877)
-- Name: lifeCycle_event_eventType_idx; Type: INDEX; Schema: lifeCycle; Owner: maarch
--

CREATE INDEX "lifeCycle_event_eventType_idx" ON "lifeCycle".event USING btree ("eventType");


--
-- TOC entry 2429 (class 1259 OID 536876)
-- Name: lifeCycle_event_instanceName_idx; Type: INDEX; Schema: lifeCycle; Owner: maarch
--

CREATE INDEX "lifeCycle_event_instanceName_idx" ON "lifeCycle".event USING btree ("instanceName");


--
-- TOC entry 2430 (class 1259 OID 536875)
-- Name: lifeCycle_event_objectClass_idx; Type: INDEX; Schema: lifeCycle; Owner: maarch
--

CREATE INDEX "lifeCycle_event_objectClass_idx" ON "lifeCycle".event USING btree ("objectClass");


--
-- TOC entry 2431 (class 1259 OID 536878)
-- Name: lifeCycle_event_objectClass_objectId_idx; Type: INDEX; Schema: lifeCycle; Owner: maarch
--

CREATE INDEX "lifeCycle_event_objectClass_objectId_idx" ON "lifeCycle".event USING btree ("objectClass", "objectId");


--
-- TOC entry 2335 (class 1259 OID 536525)
-- Name: medona_message_date_idx; Type: INDEX; Schema: medona; Owner: maarch
--

CREATE INDEX medona_message_date_idx ON medona.message USING btree (date);


--
-- TOC entry 2336 (class 1259 OID 536524)
-- Name: medona_message_recipientOrgRegNumber_idx; Type: INDEX; Schema: medona; Owner: maarch
--

CREATE INDEX "medona_message_recipientOrgRegNumber_idx" ON medona.message USING btree ("recipientOrgRegNumber");


--
-- TOC entry 2337 (class 1259 OID 536526)
-- Name: medona_message_status_active_idx; Type: INDEX; Schema: medona; Owner: maarch
--

CREATE INDEX medona_message_status_active_idx ON medona.message USING btree (status, active);


--
-- TOC entry 2346 (class 1259 OID 536527)
-- Name: medona_unitIdentifier_messageId_idx; Type: INDEX; Schema: medona; Owner: maarch
--

CREATE INDEX "medona_unitIdentifier_messageId_idx" ON medona."unitIdentifier" USING btree ("messageId");


--
-- TOC entry 2347 (class 1259 OID 536528)
-- Name: medona_unitIdentifier_objectClass_idx; Type: INDEX; Schema: medona; Owner: maarch
--

CREATE INDEX "medona_unitIdentifier_objectClass_idx" ON medona."unitIdentifier" USING btree ("objectClass", "objectId");


--
-- TOC entry 2370 (class 1259 OID 536641)
-- Name: archive_archivalProfileReference_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "archive_archivalProfileReference_idx" ON "recordsManagement".archive USING btree ("archivalProfileReference");


--
-- TOC entry 2371 (class 1259 OID 536644)
-- Name: archive_disposalDate_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "archive_disposalDate_idx" ON "recordsManagement".archive USING btree ("disposalDate");


--
-- TOC entry 2372 (class 1259 OID 536640)
-- Name: archive_filePlanPosition_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "archive_filePlanPosition_idx" ON "recordsManagement".archive USING btree ("filePlanPosition");


--
-- TOC entry 2373 (class 1259 OID 536643)
-- Name: archive_originatorOrgRegNumber_originatorArchiveId_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "archive_originatorOrgRegNumber_originatorArchiveId_idx" ON "recordsManagement".archive USING btree ("originatorOrgRegNumber", "originatorArchiveId");


--
-- TOC entry 2376 (class 1259 OID 536642)
-- Name: archive_status_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX archive_status_idx ON "recordsManagement".archive USING btree (status);


--
-- TOC entry 2377 (class 1259 OID 536645)
-- Name: archive_to_tsvector_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX archive_to_tsvector_idx ON "recordsManagement".archive USING gin (to_tsvector('french'::regconfig, translate(text, 'ÀÁÂÃÄÅàáâãäåÆæÞþČčĆćÇçĐđÈÉÊËèéêëÌÍÎÏìíîïÑñÒÓÔÕÖØðòóôõöøœŒŔŕŠšßÙÚÛÜùúûÝýÿŽž'::text, 'AAAAAAaaaaaaAEaeBbCcCcCcDjdjEEEEeeeeIIIIiiiiNnOOOOOOooooooooeOERrSsSsUUUUuuuYyyZz'::text)));


--
-- TOC entry 2387 (class 1259 OID 536695)
-- Name: recordsManagement_archiveRelationship_archiveId_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archiveRelationship_archiveId_idx" ON "recordsManagement"."archiveRelationship" USING btree ("archiveId");


--
-- TOC entry 2388 (class 1259 OID 536696)
-- Name: recordsManagement_archiveRelationship_relatedArchiveId_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archiveRelationship_relatedArchiveId_idx" ON "recordsManagement"."archiveRelationship" USING btree ("relatedArchiveId");


--
-- TOC entry 2378 (class 1259 OID 536692)
-- Name: recordsManagement_archive_archiverOrgRegNumber_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_archiverOrgRegNumber_idx" ON "recordsManagement".archive USING btree ("archiverOrgRegNumber");


--
-- TOC entry 2379 (class 1259 OID 536690)
-- Name: recordsManagement_archive_descriptionClass_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_descriptionClass_idx" ON "recordsManagement".archive USING btree ("descriptionClass");


--
-- TOC entry 2380 (class 1259 OID 536688)
-- Name: recordsManagement_archive_originatingDate_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_originatingDate_idx" ON "recordsManagement".archive USING btree ("originatingDate");


--
-- TOC entry 2381 (class 1259 OID 536694)
-- Name: recordsManagement_archive_originatorArchiveId_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_originatorArchiveId_idx" ON "recordsManagement".archive USING btree ("originatorArchiveId");


--
-- TOC entry 2382 (class 1259 OID 536693)
-- Name: recordsManagement_archive_originatorOrgRegNumber_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_originatorOrgRegNumber_idx" ON "recordsManagement".archive USING btree ("originatorOrgRegNumber");


--
-- TOC entry 2383 (class 1259 OID 536691)
-- Name: recordsManagement_archive_originatorOwnerOrgId_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_originatorOwnerOrgId_idx" ON "recordsManagement".archive USING btree ("originatorOwnerOrgId");


--
-- TOC entry 2384 (class 1259 OID 536689)
-- Name: recordsManagement_archive_parentArchiveId_idx; Type: INDEX; Schema: recordsManagement; Owner: maarch
--

CREATE INDEX "recordsManagement_archive_parentArchiveId_idx" ON "recordsManagement".archive USING btree ("parentArchiveId");


--
-- TOC entry 2452 (class 2606 OID 536367)
-- Name: privilege privilege_roleId_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth.privilege
    ADD CONSTRAINT "privilege_roleId_fkey" FOREIGN KEY ("roleId") REFERENCES auth.role("roleId");


--
-- TOC entry 2450 (class 2606 OID 536349)
-- Name: roleMember roleMember_roleId_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth."roleMember"
    ADD CONSTRAINT "roleMember_roleId_fkey" FOREIGN KEY ("roleId") REFERENCES auth.role("roleId");


--
-- TOC entry 2451 (class 2606 OID 536354)
-- Name: roleMember roleMember_userAccountId_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth."roleMember"
    ADD CONSTRAINT "roleMember_userAccountId_fkey" FOREIGN KEY ("userAccountId") REFERENCES auth.account("accountId");


--
-- TOC entry 2453 (class 2606 OID 536380)
-- Name: servicePrivilege servicePrivilege_accountId_fkey; Type: FK CONSTRAINT; Schema: auth; Owner: maarch
--

ALTER TABLE ONLY auth."servicePrivilege"
    ADD CONSTRAINT "servicePrivilege_accountId_fkey" FOREIGN KEY ("accountId") REFERENCES auth.account("accountId");


--
-- TOC entry 2454 (class 2606 OID 536432)
-- Name: address address_contactId_fkey; Type: FK CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.address
    ADD CONSTRAINT "address_contactId_fkey" FOREIGN KEY ("contactId") REFERENCES contact.contact("contactId");


--
-- TOC entry 2456 (class 2606 OID 536462)
-- Name: communication communication_comMeanCode_fkey; Type: FK CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.communication
    ADD CONSTRAINT "communication_comMeanCode_fkey" FOREIGN KEY ("comMeanCode") REFERENCES contact."communicationMean"(code);


--
-- TOC entry 2455 (class 2606 OID 536457)
-- Name: communication communication_contactId_fkey; Type: FK CONSTRAINT; Schema: contact; Owner: maarch
--

ALTER TABLE ONLY contact.communication
    ADD CONSTRAINT "communication_contactId_fkey" FOREIGN KEY ("contactId") REFERENCES contact.contact("contactId");


--
-- TOC entry 2472 (class 2606 OID 536755)
-- Name: address address_repositoryId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".address
    ADD CONSTRAINT "address_repositoryId_fkey" FOREIGN KEY ("repositoryId") REFERENCES "digitalResource".repository("repositoryId");


--
-- TOC entry 2471 (class 2606 OID 536750)
-- Name: address address_resId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".address
    ADD CONSTRAINT "address_resId_fkey" FOREIGN KEY ("resId") REFERENCES "digitalResource"."digitalResource"("resId");


--
-- TOC entry 2473 (class 2606 OID 536768)
-- Name: clusterRepository clusterRepository_clusterId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."clusterRepository"
    ADD CONSTRAINT "clusterRepository_clusterId_fkey" FOREIGN KEY ("clusterId") REFERENCES "digitalResource".cluster("clusterId");


--
-- TOC entry 2474 (class 2606 OID 536773)
-- Name: clusterRepository clusterRepository_repositoryId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."clusterRepository"
    ADD CONSTRAINT "clusterRepository_repositoryId_fkey" FOREIGN KEY ("repositoryId") REFERENCES "digitalResource".repository("repositoryId");


--
-- TOC entry 2468 (class 2606 OID 536714)
-- Name: digitalResource digitalResource_archiveId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."digitalResource"
    ADD CONSTRAINT "digitalResource_archiveId_fkey" FOREIGN KEY ("archiveId") REFERENCES "recordsManagement".archive("archiveId");


--
-- TOC entry 2469 (class 2606 OID 536719)
-- Name: digitalResource digitalResource_clusterId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."digitalResource"
    ADD CONSTRAINT "digitalResource_clusterId_fkey" FOREIGN KEY ("clusterId") REFERENCES "digitalResource".cluster("clusterId");


--
-- TOC entry 2470 (class 2606 OID 536724)
-- Name: digitalResource digitalResource_relatedResId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."digitalResource"
    ADD CONSTRAINT "digitalResource_relatedResId_fkey" FOREIGN KEY ("relatedResId") REFERENCES "digitalResource"."digitalResource"("resId");


--
-- TOC entry 2475 (class 2606 OID 536786)
-- Name: package package_packageId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource".package
    ADD CONSTRAINT "package_packageId_fkey" FOREIGN KEY ("packageId") REFERENCES "digitalResource"."digitalResource"("resId");


--
-- TOC entry 2476 (class 2606 OID 536797)
-- Name: packedResource packedResource_packageId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."packedResource"
    ADD CONSTRAINT "packedResource_packageId_fkey" FOREIGN KEY ("packageId") REFERENCES "digitalResource".package("packageId");


--
-- TOC entry 2477 (class 2606 OID 536802)
-- Name: packedResource packedResource_resId_fkey; Type: FK CONSTRAINT; Schema: digitalResource; Owner: maarch
--

ALTER TABLE ONLY "digitalResource"."packedResource"
    ADD CONSTRAINT "packedResource_resId_fkey" FOREIGN KEY ("resId") REFERENCES "digitalResource"."digitalResource"("resId");


--
-- TOC entry 2478 (class 2606 OID 536838)
-- Name: folder folderId_filePlan_fkey; Type: FK CONSTRAINT; Schema: filePlan; Owner: maarch
--

ALTER TABLE ONLY "filePlan".folder
    ADD CONSTRAINT "folderId_filePlan_fkey" FOREIGN KEY ("parentFolderId") REFERENCES "filePlan".folder("folderId");


--
-- TOC entry 2479 (class 2606 OID 536849)
-- Name: position position_filePlan_fkey; Type: FK CONSTRAINT; Schema: filePlan; Owner: maarch
--

ALTER TABLE ONLY "filePlan"."position"
    ADD CONSTRAINT "position_filePlan_fkey" FOREIGN KEY ("folderId") REFERENCES "filePlan".folder("folderId");


--
-- TOC entry 2457 (class 2606 OID 536498)
-- Name: messageComment messageComment_messageId_fkey; Type: FK CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."messageComment"
    ADD CONSTRAINT "messageComment_messageId_fkey" FOREIGN KEY ("messageId") REFERENCES medona.message("messageId");


--
-- TOC entry 2458 (class 2606 OID 536511)
-- Name: unitIdentifier unitIdentifier_messageId_fkey; Type: FK CONSTRAINT; Schema: medona; Owner: maarch
--

ALTER TABLE ONLY medona."unitIdentifier"
    ADD CONSTRAINT "unitIdentifier_messageId_fkey" FOREIGN KEY ("messageId") REFERENCES medona.message("messageId");


--
-- TOC entry 2485 (class 2606 OID 536958)
-- Name: archivalProfileAccess archivalProfileAccess_orgId_fkey; Type: FK CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."archivalProfileAccess"
    ADD CONSTRAINT "archivalProfileAccess_orgId_fkey" FOREIGN KEY ("orgId") REFERENCES organization.organization("orgId");


--
-- TOC entry 2484 (class 2606 OID 536944)
-- Name: orgContact orgContact_orgId_fkey; Type: FK CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."orgContact"
    ADD CONSTRAINT "orgContact_orgId_fkey" FOREIGN KEY ("orgId") REFERENCES organization.organization("orgId");


--
-- TOC entry 2480 (class 2606 OID 536900)
-- Name: organization organization_orgTypeCode_fkey; Type: FK CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization.organization
    ADD CONSTRAINT "organization_orgTypeCode_fkey" FOREIGN KEY ("orgTypeCode") REFERENCES organization."orgType"(code);


--
-- TOC entry 2482 (class 2606 OID 536910)
-- Name: organization organization_ownerOrgId_fkey; Type: FK CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization.organization
    ADD CONSTRAINT "organization_ownerOrgId_fkey" FOREIGN KEY ("ownerOrgId") REFERENCES organization.organization("orgId");


--
-- TOC entry 2481 (class 2606 OID 536905)
-- Name: organization organization_parentOrgId_fkey; Type: FK CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization.organization
    ADD CONSTRAINT "organization_parentOrgId_fkey" FOREIGN KEY ("parentOrgId") REFERENCES organization.organization("orgId");


--
-- TOC entry 2483 (class 2606 OID 536923)
-- Name: userPosition userPosition_orgId_fkey; Type: FK CONSTRAINT; Schema: organization; Owner: maarch
--

ALTER TABLE ONLY organization."userPosition"
    ADD CONSTRAINT "userPosition_orgId_fkey" FOREIGN KEY ("orgId") REFERENCES organization.organization("orgId");


--
-- TOC entry 2462 (class 2606 OID 536581)
-- Name: archivalProfileContents archivalProfileContents_containedProfileId_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfileContents"
    ADD CONSTRAINT "archivalProfileContents_containedProfileId_fkey" FOREIGN KEY ("containedProfileId") REFERENCES "recordsManagement"."archivalProfile"("archivalProfileId");


--
-- TOC entry 2461 (class 2606 OID 536576)
-- Name: archivalProfileContents archivalProfileContents_parentProfileId_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfileContents"
    ADD CONSTRAINT "archivalProfileContents_parentProfileId_fkey" FOREIGN KEY ("parentProfileId") REFERENCES "recordsManagement"."archivalProfile"("archivalProfileId");


--
-- TOC entry 2459 (class 2606 OID 536558)
-- Name: archivalProfile archivalProfile_accessRuleCode_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfile"
    ADD CONSTRAINT "archivalProfile_accessRuleCode_fkey" FOREIGN KEY ("accessRuleCode") REFERENCES "recordsManagement"."accessRule"(code);


--
-- TOC entry 2460 (class 2606 OID 536563)
-- Name: archivalProfile archivalProfile_retentionRuleCode_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archivalProfile"
    ADD CONSTRAINT "archivalProfile_retentionRuleCode_fkey" FOREIGN KEY ("retentionRuleCode") REFERENCES "recordsManagement"."retentionRule"(code);


--
-- TOC entry 2463 (class 2606 OID 536606)
-- Name: archiveDescription archiveDescription_archivalProfileId_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archiveDescription"
    ADD CONSTRAINT "archiveDescription_archivalProfileId_fkey" FOREIGN KEY ("archivalProfileId") REFERENCES "recordsManagement"."archivalProfile"("archivalProfileId");


--
-- TOC entry 2466 (class 2606 OID 536654)
-- Name: archiveRelationship archiveRelationship_archiveId_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archiveRelationship"
    ADD CONSTRAINT "archiveRelationship_archiveId_fkey" FOREIGN KEY ("archiveId") REFERENCES "recordsManagement".archive("archiveId");


--
-- TOC entry 2467 (class 2606 OID 536659)
-- Name: archiveRelationship archiveRelationship_relatedArchiveId_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement"."archiveRelationship"
    ADD CONSTRAINT "archiveRelationship_relatedArchiveId_fkey" FOREIGN KEY ("relatedArchiveId") REFERENCES "recordsManagement".archive("archiveId");


--
-- TOC entry 2465 (class 2606 OID 536635)
-- Name: archive archive_accessRuleCode_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement".archive
    ADD CONSTRAINT "archive_accessRuleCode_fkey" FOREIGN KEY ("accessRuleCode") REFERENCES "recordsManagement"."accessRule"(code);


--
-- TOC entry 2464 (class 2606 OID 536630)
-- Name: archive archive_parentArchiveId_fkey; Type: FK CONSTRAINT; Schema: recordsManagement; Owner: maarch
--

ALTER TABLE ONLY "recordsManagement".archive
    ADD CONSTRAINT "archive_parentArchiveId_fkey" FOREIGN KEY ("parentArchiveId") REFERENCES "recordsManagement".archive("archiveId");


-- Completed on 2019-08-23 14:03:55 CEST

--
-- PostgreSQL database dump complete
--
