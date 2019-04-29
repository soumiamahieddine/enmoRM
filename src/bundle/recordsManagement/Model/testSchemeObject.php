<?php
namespace bundle\recordsManagement\Model;
class testSchemeObject
{
    /**
     * Chaîne
     * @var string
     */
    public $stringMetadata;

    /**
     * Enum 
     * @var string
     * @enumeration [foo, bar]
     */
    public $enumMetadata;

    /**
     * Date
     * @var date
     */
    public $dateMetadata;

    /**
     * Nombre 
     * @var integer
     */
    public $numberMetadata;

    /**
     * Indicateur
     * @var boolean
     */
    public $boolMetadata;
}
