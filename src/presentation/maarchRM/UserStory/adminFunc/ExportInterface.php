<?php
namespace presentation\maarchRM\UserStory\adminFunc;

/**
 * User story export
 */
interface ExportInterface
{
    /**
     * Get user account infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportUseraccount();

    /**
     * Get service accounts infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportServiceaccount();

    /**
     * Get roles infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportRole();

    /**
     * Get organizations infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportOrganization();

    /**
     * Get archival profiles infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportArchivalprofile();

    /**
     * Get description fields infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportDescriptionfield();

    /**
     * Get retention rules infos
     *
     * @uses importExport/Export/read_dataType_
     *
     */
    public function readExportRetentionrule();
}
