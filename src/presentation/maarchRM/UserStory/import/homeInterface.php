<?php

namespace presentation\maarchRM\UserStory\import;

interface homeInterface
{
    /**
     * @requires [import/archivalProfile, import/descriptionField, import/organization, import/retentionRule, import/serviceAccount, import/userAccount]
     *
     * @return importExport/Import/home
     */
    public function readImport();
}
