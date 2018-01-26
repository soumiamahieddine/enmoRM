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
     * @param string $orgRegNumber
     * @param string $folderId
     * 
     * @uses recordsManagement/archives/readFolder
     * @return recordsManagement/welcome/folderContents
     */
    public function readFolder($orgRegNumber, $folderId=false);

    /**
     * Retrieve archive info
     *
     * @return recordsManagement/welcome/archiveInfo
     * @uses  recordsManagement/archiveDescription/read_archiveId_
     */
    public function readArchive_archiveId_();

    /**
     * Retrieve archive contents
     *
     * @return recordsManagement/welcome/archiveContent
     * @uses  recordsManagement/archives/readArchivecontents_archive_
     */
    public function readArchivecontents_archive_();

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
        $description = null,
        $text = null
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
