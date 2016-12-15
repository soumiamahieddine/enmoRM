<?php
namespace core\Type;
/**
 * Class for assoc or indexed values
 */
class ArrayObject
    extends \ArrayObject
{
    
    /**
     * Get string version
     * @return string
     */
    public function __toString() {
        
        return trim($this->dataToString($this->getArrayCopy()));
    } 

    protected function dataToString($array, $shift='', $prefix='') {
        
        $str = "";

        foreach ($array as $name => $value) {
            $str .= $prefix."$name : ";

            $nameShifting = '';
            for($i=0; $i<strlen($prefix."$name : "); $i++) {
                $nameShifting .= " ";
            }

            if (is_bool($value)) {
                $str.= ($value) ? 'true' : 'false';

            } elseif (is_scalar($value)) {
                $str.= str_replace("\n", "\n$nameShifting", $value);

            } else {
                $value = (array) $value;

                $str.= '[' . PHP_EOL;
                $str.= $this->arrayToString($value, $shift.'  ');
                $str.= ']';

            }

            $str.= PHP_EOL;
        }

        return $str;
    }


    protected function arrayToString($array, $shift) {
        $str = '';
        $assoc = (\laabs\is_assoc($array)) ?  true : false;

        foreach ($array as $key => $value) {
            if ($assoc) {
                $str .= $shift."- $key : ";
            } else {
                $str .= $shift."- ";
            }

            if (is_scalar($value)) {
                $str .= $value . PHP_EOL;
            } else {
                $str.= '[' . PHP_EOL;
                    $str .= $this->arrayToString((array) $value, $shift.'  ', '- ');
                $str.= $shift.']' . PHP_EOL;
            }
        }

        return $str;
    }

} 