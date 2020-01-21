<?php

namespace presentation\maarchRM\UserStory\Export;

interface organizationInterface
{
    /**
     * Get organizations infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportOrganization();
}
