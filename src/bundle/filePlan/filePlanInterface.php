<?php

namespace bundle\filePlan;

/**
 * Interface for file plan administration
 */
interface filePlanInterface
{

    /**
     * Get the file plan's list
     *
     * @action filePlan/filePlan/getTree
     */
    public function readTree();

    /**
     * Create a folder
     * @param object $folder The new folder
     *
     * @action filePlan/filePlan/create
     */
    public function create($folder);

    /**
     * Update a folder
     * @param object $folder The folder to update
     * 
     * @action filePlan/filePlan/update
     */
    public function update($folder);

    /**
     * Move a folder on a new position
     * @param string $parentFolderId
     * 
     * @action filePlan/filePlan/move
     */
    public function updateMove_folderId_($parentFolderId=null);

    /**
     * Delete a folder
     * 
     * @action filePlan/filePlan/delete
     */
    public function delete_folder_();
}
