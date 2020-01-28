<?php

namespace presentation\maarchRM\UserStory\Import;

interface organizationInterface
{
    /**
     * Import a csv file with organization informations
     *
     * @param string  $csv      Data base64 encoded or not in proper format
     * @param boolean $isReset  Reset tables or not
     *
     * @uses importExport/Import/create_dataType_
     *
     */
    public function createImportOrganization($csv, $isReset = false);
}
