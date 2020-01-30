<?php

namespace presentation\maarchRM\UserStory\Import;

interface archivalProfileInterface
{
    /**
     * Import a csv file with archival profile informations
     *
     * @param string  $data      Data base64 encoded or not in proper format
     * @param boolean $isReset  Reset tables or not
     *
     * @uses recordsManagement/archivalProfile/createImport
     * @return importExport/Import/import
     *
     */
    public function createImportArchivalprofiles($data, $isReset = false);
}
