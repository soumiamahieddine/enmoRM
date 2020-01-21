<?php

namespace presentation\maarchRM\UserStory\Import;

interface archivalProfileInterface
{
    /**
     * Import a csv file with archival profile informations
     *
     * @param string  $csv      Data base64 encoded or not in proper format
     * @param boolean $isReset  Reset tables or not
     *
     * @uses importExport/Import/create_dataType_
     *
     */
    public function createImportArchivalprofile($csv, $isReset = false);
}
