<?php

namespace dependency\fulltext;

/**
 * The search result
 *
 * @author Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class Hit
{
    /**
     * @var string
     */
    public $index;

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $score;

    /**
     * @var array
     */
    public $fields;

    /**
     * Get field names
     * 
     * @return array
     */
    public function getFieldNames()
    {
        $names = [];

        foreach ($this->fields as $field) {
            $names[] = $field->name;
        }

        return $names;
    }

    /**
     * Check field by name
     * @param string $name
     * 
     * @return boolean
     */
    public function hasField($name)
    {
        foreach ($this->fields as $field) {
            if ($field->name == $name) {
                return true;
            }
        }
    }

    /**
     * Get field by name
     * @param string $name
     * 
     * @return mixed
     */
    public function getField($name)
    {
        foreach ($this->fields as $field) {
            if ($field->name == $name) {
                return $field;
            }
        }
    }

    /**
     * Get field value by name
     * @param string $name
     * 
     * @return mixed
     */
    public function getValue($name)
    {
        foreach ($this->fields as $field) {
            if ($field->name == $name) {
                return $field->value;
            }
        }
    }

    /**
     * Unset a field by name
     * @param string $name
     * 
     * @return mixed
     */
    public function unsetField($name)
    {
        foreach ($this->fields as $i => $field) {
            if ($field->name == $name) {
                unset($this->fields[$i]);
            }
        }
    }
}
