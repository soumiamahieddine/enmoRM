<?php

namespace bundle\filePlan\Model;
/**
 * position definition
 * 
 * @package filePlan
 * 
 * @fkey [subjectId] filePlan/subject[subjectId]
 * @fkey [objectId] recordsManagement/archive[archiveId]
 */
class position
{
    /**
     * @var id
     */
    public $objectId;

    /**
     * @var string
     * @notempty
     */
    public $label;

    /**
     * @var string
     * @notempty
     */
    public $objectClass;

    /**
     * @var id
     * @notempty
     */
    public $subjectId;
}