<?php

namespace presentation\maarchRM\UserStory\export;

interface exportRoleInterface
{
    /**
     * Get roles infos
     *
     * @uses auth/role/readExport
     *
     * @return importExport/Export/listCsv
     *
     */
    public function readExportRoles($limit = null, $ref = null);
}
