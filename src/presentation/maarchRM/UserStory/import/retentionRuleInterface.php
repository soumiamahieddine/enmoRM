<?php

namespace presentation\maarchRM\UserStory\Import;

interface retentionRuleInterface
{
    /**
     * Import a csv file with retention rules informations
     *
     * @param string  $data      Data base64 encoded or not in proper format
     * @param boolean $isReset  Reset tables or not
     *
     * @uses recordsManagement/retentionRule/createImport
     *
     */
    public function createImportRetentionrules($data, $isReset = false);
}
