<?php

namespace presentation\maarchRM\UserStory\Export;

interface retentionRuleInterface
{
    /**
     * Get retention rules infos
     *
     * @uses recordsManagement/retentionRule/readExport
     *
     * @return importExport/Export/listCsv
     *
     */
    public function readExportRetentionrules($limit = null, $ref = null);
}
