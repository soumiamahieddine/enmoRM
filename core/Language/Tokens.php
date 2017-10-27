<?php

namespace core\Language;

class Tokens
    extends \SplDoublyLinkedList
{

    public function getArrayCopy()
    {
        $array = array();
        while ($this->valid()) {
            $array[$this->key()] = $this->current();
            $this->next();
        }
        $this->rewind();
        
        return $array;
    }

}