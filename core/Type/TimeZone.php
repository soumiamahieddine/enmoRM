<?php
namespace core\Type;
/**
 * Class for time zone type
 */
class TimeZone
    extends \DateTimeZone
{
    
    const OFFSET_ISO = 1;
    const OFFSET_W3C = 1;

    /**
     * Construct a new date object
     * @param string $timezone
     */
    public function __construct($timezone=null)
    {
        if (!$timezone) {
            $timezone = date_default_timezone_get();
        }

        parent::__construct($timezone);

    }

    /**
     * Get offset with Uthe given date or UTC
     * @param DateTimeInterface $dateTime The dat to get offset of. Default is current timestamp
     * @param string            $format   The offset format (iso: +0000, w3c: +00:00)
     * 
     * @return int The offset in seconds
     */
    public function getOffset($dateTime=null, $format=self::OFFSET_ISO)
    {
        if (!$dateTime) {
            $dateTime = new Timestamp();
        }

        $offset = parent::getOffset($dateTime);

        if ($format) {
            if ($offset < 0) {
                $inv = "-";
            } else {
                $inv = "";
            }

            $h = str_pad(floor(abs($offset) / 3600), 2, "0", \STR_PAD_LEFT);
            $m = str_pad(floor(abs($offset) % 3600), 2, "0", \STR_PAD_LEFT);
            
            switch($format) {
                case self::OFFSET_ISO:
                    $offset = $inv . $h . $m;
                    break;

                case self::OFFSET_W3C:
                    $offset = $inv . $h . ":" . $m;
                    break;
            }
        }

        return $offset;
    }


}