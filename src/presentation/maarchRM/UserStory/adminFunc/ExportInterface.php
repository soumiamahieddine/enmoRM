<?php
namespace presentation\maarchRM\UserStory\adminFunc;

/**
 * User story export
 */
interface ExportInterface
{
    /**
     * Get all contacts
     *
     * @param  string $dataType Type of data to visualize (organization, user, etc)
     *
     * @uses importExport/Export/read
     *
     */
    public function readExport($dataType);
}
