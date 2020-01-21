<?php

namespace presentation\maarchRM\UserStory\Export;

interface userAccountInterface
{
    /**
     * Get user account infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportUseraccount();
}
