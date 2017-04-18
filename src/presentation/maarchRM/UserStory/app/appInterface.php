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
}
