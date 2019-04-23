<?php
namespace bundle\recordsManagement\Model;
class testScheme
{
    /**
     * Chaîne de caractères
     * 
     * @var string
     */
    public $stringMetadata;

    /**
     * Enumération
     * 
     * @var string
     * @enumeration [foo, bar]
     */
    public $enumMetadata;

    /**
     * Date
     * 
     * @var date
     */
    public $dateMetadata;

    /**
     * Nombre
     * 
     * @var integer
     */
    public $numberMetadata;

    /**
     * Booléen
     * 
     * @var boolean
     */
    public $boolMetadata;

    /**
     * Tableau de chaînes
     * 
     * @var string[]
     */
    public $stringArrayMetadata;

    /**
     * Objet
     * 
     * @var recordsManagement/testSchemeObject
     */
    public $objectMetadata;

    /**
     * Tableau d'objets
     * 
     * @var recordsManagement/testSchemeObject[]
     */
    public $objectArrayMetadata;
}
