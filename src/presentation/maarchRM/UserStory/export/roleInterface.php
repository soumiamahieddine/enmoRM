<?php

namespace presentation\maarchRM\UserStory\Export;

interface roleInterface
{
    /**
     * Get roles infos
     *
     * @uses auth/role/readExport
     *
     * @return importExport/Export/listCsv
     *
     */
    public function readExportRoles($limit = null);

    /**
     * Get roles infos
     *
     * @uses auth/role/readExport
     *
     * @return importExport/Export/export
     *
     */
    public function readExportallRoles($limit = null);
}
