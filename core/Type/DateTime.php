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

    /**
     * Construct a new date object
     * @param mixed  $datetime Either a date string or an existing datetime object
     * @param mixed  $timezone Either a timezone name or a timezone object
     * @param string $format   The default format for __toString
     */
    public function __construct($datetime=null, $timezone=null, $format=false)
    {
        // Default value for datetime is year+month+day hour+minute+second
        if (!$datetime) {
            //$datetime = date("Y-m-d H:i:s");
            $datetime = 'now';
        } else {
            $datetime = str_replace("/", "-", $datetime);
            $datetime = str_replace(",", ".", $datetime);
        }

        if (!$timezone) {
            $timezone = new TimeZone();
        } elseif (is_string($timezone)) {
            $timezone = new TimeZone($timezone);
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