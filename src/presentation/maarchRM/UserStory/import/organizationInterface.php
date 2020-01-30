<?php

namespace presentation\maarchRM\UserStory\Import;

interface organizationInterface
{
    /**
     * Import a csv file with organization informations
     *
     * @param string  $data      Data base64 encoded or not in proper format
     * @param boolean $isReset  Reset tables or not
     *
     * @uses organization/organization/createImport
     * @return importExport/Import/import
     *
     */
    public function createImportOrganizations($data, $isReset = false);
}
