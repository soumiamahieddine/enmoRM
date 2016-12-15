<?php
namespace core\Language;
/**
 * Represents an expression in given language
 */
class LanguageExpression
{
    use \core\ReadonlyTrait;
    
    /**
     * The language
     *
     * @var string
     */
    public $language;

    /**
     * The value
     *
     * @var mixed
     */
    public $value;
    
    
    /**
     * The class constructor
     * @param mixed $language
     * @param mixed $value
     */
    public function __construct($language, $value)
    {       
        $this->language = $language;
        $this->value = $value;
    }

}
