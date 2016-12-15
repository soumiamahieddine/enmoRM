<?php
namespace core\Type;
/**
 * Class for Numbers
 */
class Number
    implements \JsonSerializable
{
    
    protected $value;

    protected $decimals = 0;

    protected $decimalSep=".";

    protected $thousandsSep=",";

    /**
     * Construct a new number object
     * @param string $value
     * @param int    $decimals
     * @param string $decimalSep
     * @param string $thousandsSep
     */
    public function __construct($value, $decimals=false, $decimalSep=false, $thousandsSep=false)
    {
        if (!is_numeric($value)) {
            throw new \core\Exception("Invalid number '$value'");
        }

        $this->value = $value;

        if (!$decimals) {
            $decimals = \laabs::getNumberDecimals();
        }
        $this->decimals = $decimals;

        if (!$decimalSep) {
            $decimalSep = \laabs::getNumberDecimalSeparator();
        }
        $this->decimalSep = $decimalSep;

        if (!$thousandsSep) {
            $thousandsSep = \laabs::getNumberthousandsSeparator();
        }
        $this->thousandsSep = $thousandsSep;
    }

    /**
     * Get string
     * @return string
     */
    public function __toString()
    {
        return $this->format();
    }

    /**
     * Serialize into json representation
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }

    /**
     * Get integer
     * @return string
     */
    public function __toInt()
    {
        return (int) $this->value;
    }

    /**
     * Get float
     * @return string
     */
    public function __toFloat()
    {
        return (float) $this->value;
    }

    /**
     * Format the number
     * @param int    $decimals
     * @param string $decimalSep
     * @param string $thousandsSep
     * 
     * @return string
     */
    public function format($decimals=false, $decimalSep=false, $thousandsSep=false)
    {
        if ($decimals === false) {
            $decimals = (integer) $this->decimals;
        }
        if ($decimals === false) {
            $parts = explode('.', $this->value);
            if (isset($parts[1])) {
                $decimals = strlen($parts[1]);
            }
        }

        if ($decimalSep === false) {
            $decimalSep = $this->decimalSep;
        }

        if ($thousandsSep === false) {
            $thousandsSep = $this->thousandsSep;
        }

        return \number_format($this->value, $decimals, $decimalSep, $thousandsSep);
    }

    /** 
     * Add a value
     * @param mixed $value The value to add
     * 
     * @return float The new value
     */
    public function add($value)
    {
        $this->value += $value;

        return $this->value;
    }

    /** 
     * Substract a value
     * @param mixed $value The value to sub
     * 
     * @return float The new value
     */
    public function sub($value)
    {
        $this->value -= $value;

        return $this->value;
    }

    /** 
     * Multiply a value
     * @param mixed $value The value to multiply
     * 
     * @return float The new value
     */
    public function mul($value)
    {
        $this->value *= $value;

        return $this->value;
    }

    /** 
     * Divide by a value
     * @param mixed $value The value to divide value with
     * 
     * @return float The new value
     */
    public function div($value)
    {
        $this->value /= $value;

        return $this->value;
    }

    /** 
     * Calc mod of the value
     * @param mixed $value The value for mod
     * 
     * @return float The new value
     */
    public function mod($value)
    {
        $this->value %= $value;

        return $this->value;
    }

}