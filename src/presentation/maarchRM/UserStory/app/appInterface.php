<?php
namespace presentation\maarchRM\UserStory\app;

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
     * No privilege
     *
     * @return auth/authentication/noPrivilege
     */
    public function readNoprivilege();

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
     * Add a new folder
     * @param object $folder The new folder
     *
     * @uses filePlan/filePlan/create
     * @return filePlan/filePlan/create
     */
    public function createFileplanFolder($folder);

    /**
     * Update a folder
     * @param object $folder The new folder
     *
     * @uses filePlan/filePlan/update
     * @return filePlan/filePlan/update
     */
    public function updateFileplanFolder($folder);

    /**
     * Move a folder on a new position
     * @param string $parentFolderId
     * 
     * @uses filePlan/filePlan/updateMove_folderId_
     * @return filePlan/filePlan/move
     */
    public function updateFileplanMove_folderId_($parentFolderId=null);
}
