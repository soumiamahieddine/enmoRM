<?php

namespace presentation\maarchRM\UserStory\Import;

interface userAccountInterface
{
    /**
     * Import a csv file with user account informations
     *
     * @param string  $csv      Data base64 encoded or not in proper format
     * @param boolean $isReset  Reset tables or not
     *
     * @uses importExport/Import/create_dataType_
     *
     */
    public function createImportUseraccount($csv, $isReset = false);
}
