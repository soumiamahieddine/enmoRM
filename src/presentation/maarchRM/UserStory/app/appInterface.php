<?php
namespace presentation\maarchRM\UserStory\app;
use bundle\organization\Controller\organization;

/**
 * Interface for user administration
 */
interface appInterface
{
    /**
     * Welcome page
     *
     * @return recordsManagement/welcome/welcomePage
     */
    public function read();

    /**
     * Folder contents
     * @param string  $originatorOrgRegNumber The organization registration number
     * @param string  $filePlanPosition       The file plan position
     * @param boolean $archiveUnit            List the archive unit
     * 
     * @uses recordsManagement/archives/readList
     * @return recordsManagement/welcome/folderContents
     */
    public function readFolder($originatorOrgRegNumber, $filePlanPosition = null, $archiveUnit = false);

    /**
     * Retrieve archive info
     *
     * @return recordsManagement/welcome/archiveInfo
     * @uses  recordsManagement/archive/readMetadata_archiveId_
     */
    public function readArchive_archiveId_();

    /**
     * Retrieve archive contents
     *
     * @return recordsManagement/welcome/archiveContent
     * @uses  recordsManagement/archive/readListchildrenarchive_archiveId_
     */
    public function readArchivecontents_archiveId_();

    /**
     * Retrieve archive info
     *
     * @return recordsManagement/welcome/documentInfo
     */
    public function readDocumentinfo();

    /**
     * Retrieve an archive resource by its id
     *
     * @return recordsManagement/archive/getContents
     * @uses  recordsManagement/archive/readConsultation_archiveId_Digitalresource_resId_
     */
    public function readRecordsmanagement_archiveId_Digitalresource_resId_();

    /**
     * Display a preview of the contents
     *
     * @return digitalResource/digitalResource/retrieve
     * @uses  recordsManagement/archive/readConsultation_archiveId_Digitalresource_resId_
     */
    public function read_archiveId_Digitalresource_resId_();

    /**
     * Search form
     * @param string $archiveId
     * @param string $profileReference
     * @param string $status
     * @param string $archiveName
     * @param string $agreementReference
     * @param string $archiveExpired
     * @param string $finalDisposition
     * @param string $originatorOrgRegNumber
     * @param string $originatorOwnerOrgId
     * @param string $originatorArchiveId
     * @param array  $originatingDate
     * @param string $filePlanPosition
     * @param bool   $hasParent
     * @param string $description
     * @param string $text
     * @param bool   $partialRetentionRule
     * @param string $retentionRuleCode
     * @param string $depositStartDate
     * @param string $depositEndDate
     * @param string $originatingStartDate
     * @param string $originatingEndDate
     *
     * @uses recordsManagement/archives/read
     *
     * @return recordsManagement/welcome/folderContents
     */
    public function readRecordsmanagementArchivesSearch(
        $archiveId = null,
        $profileReference = null,
        $status = null,
        $archiveName = null,
        $agreementReference = null,
        $archiveExpired = null,
        $finalDisposition = null,
        $originatorOrgRegNumber = null,
        $originatorOwnerOrgId = null,
        $originatorArchiveId = null,
        $originatingDate = null,
        $filePlanPosition = null,
        $hasParent = null,
        $description = null,
        $text = null,
        $partialRetentionRule = null,
        $retentionRuleCode = null,
        $depositStartDate = null,
        $depositEndDate = null,
        $originatingStartDate = null,
        $originatingEndDate = null
    );

    /**
     * Move an archive into a folder
     * @param array  $archiveIds   The archive identifier list
     * @param string $fromFolderId The originating folder identifier
     * @param string $toFolderId   The destination folder identifier
     * 
     * @return recordsManagement/welcome/moveArchivesToFolder
     * @uses recordsManagement/archives/udpateMovearchivestofolder
     */
    public function updateArchivesMovetofolder($archiveIds, $fromFolderId=null, $toFolderId=null);

    /**
     * Search formats
     *
     * @uses digitalResource/format/readFind_query_
     * @return digitalResource/format/find
     */
    public function readDigitalresourceFormatFind_query_();

    /**
     * Get the organizations' list
     *
     * @return organization/orgTree/orgList
     * @uses organization/organization/readTodisplay
     */
    public function readOrganizationsTodisplay();

    /**
     * Get the organizations' list
     *
     * @return organization/orgTree/orgList
     * @uses organization/organization/readTodisplayOrgUnit
     */
    public function readOrganizationsTodisplayorgunit();

    /**
     * Get the producer' list
     *
     * @uses organization/organization/readOriginator
     */
    public function readOriginator();
}
