<?php

namespace presentation\maarchRM\UserStory\Export;

interface serviceAccountInterface
{
    /**
     * Get service accounts infos
     *
     * @uses auth/serviceAccount/readExport
     *
     * @return importExport/Export/listCsv
     *
     */
    public function readExportServiceaccounts($limit = null);

    /**
     * Get service accounts infos
     *
     * @uses auth/serviceAccount/readExport
     *
     * @return importExport/Export/export
     *
     */
    public function readExportallServiceaccounts($limit = null);
}
