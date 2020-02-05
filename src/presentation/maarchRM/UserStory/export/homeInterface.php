<?php

namespace presentation\maarchRM\UserStory\Export;

interface homeInterface
{
    /**
     * @requires [export/archivalProfile, export/descriptionField, export/organization, export/retentionRule, export/serviceAccount, export/userAccount]
     *
     * @return importExport/Export/home
     */
    public function readExport();
}
