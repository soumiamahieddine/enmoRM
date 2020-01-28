<?php

namespace presentation\maarchRM\UserStory\Export;

interface descriptionFieldInterface
{
    /**
     * Get description fields infos
     *
     * @uses recordsManagement/descriptionField/readExport
     *
     * @return importExport/Export/listCsv
     */
    public function readExportDescriptionfields($limit = null);

    /**
     * Get description fields infos
     *
     * @uses recordsManagement/descriptionField/readExport
     *
     * @return importExport/Export/export
     */
    public function readExportallDescriptionfields($limit = null);
}
