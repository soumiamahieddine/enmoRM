<?php

namespace presentation\maarchRM\UserStory\Import;

interface descriptionFieldInterface
{
    /**
     * Import a csv file with description field informations
     *
     * @param string  $data      Data base64 encoded or not in proper format
     * @param boolean $isReset  Reset tables or not
     *
     * @uses recordsManagement/descriptionField/createImport
     * @return importExport/Import/import
     *
     */
    public function createImportDescriptionfields($data, $isReset = false);
}
