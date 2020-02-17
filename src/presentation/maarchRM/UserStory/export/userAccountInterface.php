<?php

namespace presentation\maarchRM\UserStory\export;

interface userAccountInterface
{
    /**
     * Get user account infos
     *
     * @uses auth/userAccount/readExport
     *
     * @return importExport/Export/listCsv
     *
     */
    public function readExportUseraccounts($limit = null, $ref = null);
}
