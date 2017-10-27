<?php
namespace core\Type;
/**
 * Class for timestamp type
 * Timestamp is a date + time of day + microseconds
 * No timezone ! Timestamp is always given from UNIX epoch UTC
 */
class Timestamp
    extends DateTime
{
    protected $format;

    /**
     * Construct a new date object
     * @param string $value
     * @param string $format
     */
    public function __construct($value=null, $format=false)
    {
        // Default value for timestamp is year+month+day hour+minute+second + microsecond
        // In UTC timezone
        if (empty($value)) {
            $value = \gmdate('Y-m-d\TH:i:s') . "," . substr(microtime(), 2, 6) . "+0";
        } else {
            $value = str_replace(",", ".", $value);
        }

        $timezone = 'UTC';

        // Default format for timestamp is given by server directive
        if (!$format) {
            $format = \laabs::getTimestampFormat();
        }

        parent::__construct($value, $timezone, $format);
    }

    /**
     * Serialize into json representation
     * @return string
     */
    public function jsonSerialize()
    {
        return (string) $this->format("Y-m-d H:i:s.u");
    }


}