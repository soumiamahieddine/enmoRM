<?php

namespace presentation\maarchRM\UserStory\Export;

interface archivalProfileInterface
{
    /**
     * Get archival profiles infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportArchivalprofile();
}
