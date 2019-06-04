<?php
namespace core\Type;
/**
 * Class for integer type
 */
class Date
    extends DateTime
{

    /**
     * Construct a new date object
     * @param string $value  The date value
     * @param string $format The default format for __toString
     */
    public function __construct($value=null, $format=false)
    {
        // Default value for date is year+month+day
        // In UTC timezone
        if (!$value) {
            $value = date("Y-m-d");
        }

        // Useless (no time)
        $timezone = null;

        // Default format for date is given by server directive
        if (!$format) {
            $format = \laabs::getDateFormat();
        }

        parent::__construct($value, $timezone, $format);
    }

    /**
     * Serialize into json representation
     * @return string
     */
    public function jsonSerialize()
    {
        return (string) $this->format('Y-m-d');
    }

}