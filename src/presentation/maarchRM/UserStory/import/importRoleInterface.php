<?php

namespace presentation\maarchRM\UserStory\import;

interface importRoleInterface
{
    /**
     * Import a csv file with role informations
     *
     * @param resource  $data      Data base64 encoded or not in proper format
     * @param boolean   $isReset   Reset tables or not
     *
     * @uses auth/role/createImport
     *
     */
    public function createImportRoles($data, $isReset = false);
}
