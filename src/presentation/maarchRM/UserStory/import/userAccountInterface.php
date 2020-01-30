<?php

namespace presentation\maarchRM\UserStory\Import;

interface userAccountInterface
{
    /**
     * Import a csv file with user account informations
     *
     * @param string  $data      Data base64 encoded or not in proper format
     * @param boolean $isReset  Reset tables or not
     *
     * @uses auth/userAccount/createImport
     * @return importExport/Import/import
     *
     */
    public function createImportUseraccounts($data, $isReset = false);
}
