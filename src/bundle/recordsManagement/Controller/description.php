<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\recordsManagement\Controller;

/**
 * Control of the recordsManagement archive description
 *
 * @package RecordsManagement
 * @author  Cyril VAZQUEZ <cyril.vazquez@maarch.org> 
 */
class description
{
    protected $sdoFactory;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory
     *
     * @return void
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Create the description
     * @param mixed  $object    The description object
     * @param string $archiveId The archive identifier
     * 
     * @return bool
     */
    public function create($object, $archiveId)
    {
        $description = \laabs::newInstance('recordsManagement/description');
        $description->archiveId = $archiveId;
        $description->object = json_encode($object);
        $description->text = implode(' ', $this->getText($object));
        
        $this->sdoFactory->create($description);
    }

    protected function getText($object)
    {
        $text = [];

        foreach ($object as $name => $value) {
            if (is_string($value) || is_numeric($value)) {
                $text[] = $value;
            } elseif (is_object($value)) {
                $text = array_merge($text, $this->getText($value));
            }
        }

        return $text;
    }

    /**
     * Read the description
     * @param string $archiveId The archive identifier
     * 
     * @return object
     */
    public function read($archiveId)
    {
        try {
            $description = $this->sdoFactory->read('recordsManagement/description', $archiveId);

            return json_decode($description->object);
        } catch(\Exception $e) {
            
        }
    }

    /**
     * Find the descriptions
     * @param array $query The query parts
     * 
     * @return array
     */
    public function find($query)
    {
        $asserts = [];

        foreach ($query as $name => $arg) {
            if (is_string($arg)) {
                if (substr($arg, -1) == '*') {
                    $arg = $arg.":*";
                }
                $asserts[] = "text @@ to_tsquery('$arg')";
            } else {
                $asserts[] = "object->>'".$arg['name']."' ".$arg['op']." '".$arg['value']."'";
            }
        }

        $descriptions = $this->sdoFactory->find('recordsManagement/description', implode(' and ', $asserts));

        foreach ($descriptions as $description) {
            $description->object = json_decode($description);
        }

        return $descriptions;
    }

    /**
     * Update the description
     * @param mixed  $object    The description object
     * @param string $archiveId The archive identifier
     * 
     * @return bool
     */
    public function update($object, $archiveId)
    {
        $description = $this->read($archiveId);
        foreach ($object as $name => $value) {
            $description->object->{$name} = $value;
        }

        $description->text = implode(' ', $this->getText($object));
        
        $this->sdoFactory->update($description);
    }

    /**
     * Delete the description
     * @param string $archiveId The archive identifier
     * 
     * @return bool
     */
    public function delete($archiveId)
    {
        $this->sdoFactory->delete('recordsManagement/description', $archiveId);
    }
}