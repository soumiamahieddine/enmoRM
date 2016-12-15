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
}
