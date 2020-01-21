<?php

namespace presentation\maarchRM\UserStory\Import;

interface retentionRuleInterface
{
    /**
     * Import a csv file with retention rules informations
     *
     * @param string  $csv      Data base64 encoded or not in proper format
     * @param boolean $isReset  Reset tables or not
     *
     * @uses importExport/Import/create_dataType_
     *
     */
    public function createImportRetentionrule($csv, $isReset = false);
}
