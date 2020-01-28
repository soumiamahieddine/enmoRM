<?php

namespace presentation\maarchRM\UserStory\Export;

interface archivalProfileInterface
{
    /**
     * Get archival profiles infos
     *
     * @uses recordsManagement/archivalProfile/readExport
     *
     * @return importExport/Export/listCsv
     */
    public function readExportArchivalprofiles($limit = null);

    /**
     * Get archival profiles infos
     *
     * @uses recordsManagement/archivalProfile/readExport
     *
     * @return importExport/Export/export
     */
    public function readExportallArchivalprofiles($limit = null);
}
