<?php

namespace presentation\maarchRM\UserStory\Export;

interface serviceAccountInterface
{
    /**
     * Get service accounts infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportServiceaccount();
}
