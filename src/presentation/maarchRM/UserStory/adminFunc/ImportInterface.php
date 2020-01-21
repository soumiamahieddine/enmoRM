<?php
namespace presentation\maarchRM\UserStory\adminFunc;

/**
 * User story export
 */
interface ImportInterface
{
    /**
     * import a csv file with user account informations
     *
     * @param string  $csv      Data base64 encoded or not
     * @param boolean $isReset  Reset tables or not
     *
     * @uses importImport/Import/create_dataType_
     *
     */
    public function createImportUseraccount($csv, $isReset = false);

    /**
     * Get service accounts infos
     *
     * @param string  $csv      Data base64 encoded or not
     * @param boolean $isReset  Reset tables or not
     *
     * @uses importImport/Import/create_dataType_
     *
     */
    public function createImportServiceaccount();

    /**
     * Get roles infos
     *
     * @param string  $csv      Data base64 encoded or not
     * @param boolean $isReset  Reset tables or not
     *
     * @uses importImport/Import/create_dataType_
     *
     */
    public function createImportRole();

    /**
     * Get organizations infos
     *
     * @param string  $csv      Data base64 encoded or not
     * @param boolean $isReset  Reset tables or not
     *
     * @uses importImport/Import/create_dataType_
     *
     */
    public function createImportOrganization();

    /**
     * Get archival profiles infos
     *
     * @param string  $csv      Data base64 encoded or not
     * @param boolean $isReset  Reset tables or not
     *
     * @uses importImport/Import/create_dataType_
     *
     */
    public function createImportArchivalprofile();

    /**
     * Get description fields infos
     *
     * @param string  $csv      Data base64 encoded or not
     * @param boolean $isReset  Reset tables or not
     *
     * @uses importImport/Import/create_dataType_
     *
     */
    public function createImportDescriptionfield();

    /**
     * Get retention rules infos
     *
     * @param string  $csv      Data base64 encoded or not
     * @param boolean $isReset  Reset tables or not
     *
     * @uses importImport/Import/create_dataType_
     *
     */
    public function createImportRetentionrule();
}
