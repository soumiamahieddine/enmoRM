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
}
