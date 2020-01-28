<?php

namespace presentation\maarchRM\UserStory\Import;

interface serviceAccountInterface
{
    /**
     * Import a csv file with service account informations
     *
     * @param string  $csv      Data base64 encoded or not in proper format
     * @param boolean $isReset  Reset tables or not
     *
     * @uses importExport/Import/create_dataType_
     *
     */
    public function createImportServiceaccount($csv, $isReset = false);
}
