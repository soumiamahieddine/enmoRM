<?php

namespace presentation\maarchRM\UserStory\Export;

interface roleInterface
{
    /**
     * Get retention rules infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportRetentionrule();
}
