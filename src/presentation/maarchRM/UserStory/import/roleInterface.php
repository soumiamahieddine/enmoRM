<?php

namespace presentation\maarchRM\UserStory\Import;

interface roleInterface
{
    /**
     * Import a csv file with role informations
     *
     * @param string  $data      Data base64 encoded or not in proper format
     * @param boolean $isReset  Reset tables or not
     *
     * @uses auth/role/createImport
     * @return importExport/Import/import
     *
     */
    public function createImportRoles($data, $isReset = false);
}
