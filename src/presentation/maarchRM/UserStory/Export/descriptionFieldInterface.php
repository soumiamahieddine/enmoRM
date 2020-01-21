<?php

namespace presentation\maarchRM\UserStory\Export;

interface descriptionFieldInterface
{
    /**
     * Get description fields infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportDescriptionfield();
}
