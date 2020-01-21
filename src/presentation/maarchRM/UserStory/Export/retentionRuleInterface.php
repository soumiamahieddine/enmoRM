<?php

namespace presentation\maarchRM\UserStory\Export;

interface retentionRuleInterface
{
    /**
     * Get retention rules infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportRetentionrule();
}
