<?php

namespace presentation\maarchRM\UserStory\Export;

interface roleInterface
{
    /**
     * Get roles infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportRole();
}
