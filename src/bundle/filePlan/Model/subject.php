<?php

namespace bundle\filePlan\Model;
/**
 * subject definition
 * 
 * @package filePlan
 * 
 * @pkey [subjectId]
 * @fkey [parentSubjectId] filePlan/subject [subjectId]
 */
class subject
{
    /**
     * @var id
     */
    public $subjectId;

    /**
     * @var string
     * @notempty
     */
    public $name;
    
    /**
     * @var string
     * 
     */
    public $parentSubjectId;
    
    /**
     * @var string
     * 
     */
    public $description;
    
}