<?php
namespace bundle\recordsManagement\Model;
class testSchemeObject
{
    /**
     * @var string
     */
    public $stringMetadata;

    /**
     * @var string
     * @enumeration [foo, bar]
     */
    public $enumMetadata;

    /**
     * @var date
     */
    public $dateMetadata;

    /**
     * @var integer
     */
    public $numberMetadata;

    /**
     * @var boolean
     */
    public $boolMetadata;
}
