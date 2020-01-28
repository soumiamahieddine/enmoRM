<?php

namespace presentation\maarchRM\UserStory\Import;

interface descriptionFieldInterface
{
    /**
     * Import a csv file with description field informations
     *
     * @param string  $csv      Data base64 encoded or not in proper format
     * @param boolean $isReset  Reset tables or not
     *
     * @uses importExport/Import/create_dataType_
     *
     */
    public function createImportDescriptionfield($csv, $isReset = false);
}
