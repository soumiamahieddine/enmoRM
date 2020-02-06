<?php
namespace core\Type;
/**
 * Class for integer type
 */
class DateTime
    extends \DateTime
    implements \JsonSerializable
{
    
    protected $format;

    public $parts;

    /**
     * Construct a new date object
     * @param mixed  $datetime Either a date string or an existing datetime object
     * @param mixed  $timezone Either a timezone name or a timezone object
     * @param string $format   The default format for __toString
     */
    public function __construct($datetime = null, $timezone = null, $format = false)
    {
        // Default value for datetime is year+month+day hour+minute+second
        if (!$datetime) {
            $datetime = date("Y-m-d H:i:s");
        } else {
            $datetime = str_replace("/", "-", $datetime);
            $datetime = str_replace(",", ".", $datetime);
        }

        // Parse datetime string to keep track of received parts
        $this->parts = date_parse($datetime);
        if ($this->parts['error_count'] > 0) {
            throw new \core\Exception("Invalid date time.");
        }

        unset($this->parts['error_count']);
        unset($this->parts['errors']);
        unset($this->parts['warning_count']);
        unset($this->parts['warnings']);

        // Check if a timezone is provided on the datetime string
        if ($this->parts['is_localtime']) {
            // Ignore any received timezone parameter and use the timezone in datetime string
            $timezone = null;

            // Timezone expressed in minutes in PHP <= 7.1 with shift TO UTC from tz (Europe/Paris = -60)
            // and seconds in PHP >= 7.2 whit shift FROM UTC to tz (Europe/Paris = 7200)
            if (version_compare(PHP_VERSION, '52', '<')) {
                $this->parts['zone'] = -($this->parts['zone']*60);
            }
        } elseif (!is_null($timezone)) {
            // Default value for timezone is local server timezone
            // String timezone is converted to timezone object
            if (is_string($timezone)) {
                $timezone = new TimeZone($timezone);
            }

            $this->parts['is_localtime'] = true;
            $offset = $timezone->getOffset();
            if ($offset[0] == '-') {
                $hh = (int) substr($offset, 0, 3);
            } else {
                $hh = (int) substr($offset, 0, 2);
            }
            $mm = substr($offset, -2, 2);
            $this->parts['zone'] = ($hh*3600)+($mm*60);
        }

        if (!$format) {
            $format = \laabs::getTimestampFormat();
        }

        $this->format = $format;

        parent::__construct($datetime, $timezone);
    }

    /**
     * Magic method to retrieve value
     * @return string
     */
    public function __toString()
    {
        return (string) $this->format();
    }

    /**
     * Serialize into json representation
     * @return string
     */
    public function jsonSerialize()
    {
        return (string) $this->format('c');
    }

    /**
     * Format date
     * @param string $format
     * 
     * @return string
     */
    public function format($format=null)
    {
        if (is_null($format)) {
            $format = $this->format;
        }

        return parent::format($format);
    }

    /**
     * Calculate the difference between the date and another
     * @param \DateTimeInterface $dateTime The date to compare
     * @param boolean            $absolute Calc in absolute or relative mode
     * 
     * @return \core\Type\duration
     */
    public function diff($dateTime, $absolute=null)
    {
        $interval = parent::diff($dateTime, $absolute);

        $duration = new Duration($interval->format("P%YY%MM%DDT%HH%IM%SS"));

        $duration->invert = $interval->invert;
        $duration->days = $interval->days;

        return $duration;
    }

    /**
     * Get the timezone object 
     * @return TimeZone
     */
    public function getTimeZone()
    {
        $timezone = parent::getTimeZone();

        return new TimeZone($timezone->getName());
    }

    /**
     * Add a duration to the date annd return a new datetime object as the result
     * @param \duration $duration The duration to add
     * 
     * @return mixed The new datetime object of same class
     */
    public function shift($duration)
    {
        $newDate = clone($this);

        return $newDate->add($duration);
    }

    /**
     * Add a duration to the date annd return a new datetime object as the result
     * @param \duration $duration The duration to add
     * 
     * @return mixed The new datetime object of same class
     */
    public function unshift($duration)
    {
        $newDate = clone($this);

        return $newDate->sub($duration);
    }
}
