<?php

namespace presentation\maarchRM\UserStory\Export;

interface organizationInterface
{
    /**
     * Get organizations infos
     *
     * @uses organization/organization/readExport
     *
     * @return importExport/Export/listCsv
     */
    public function readExportOrganizations($limit = null);

    /**
     * Get organizations infos
     *
     * @uses organization/organization/readExport
     *
     * @return importExport/Export/export
     */
    public function readExportallOrganizations($limit = null);
}
