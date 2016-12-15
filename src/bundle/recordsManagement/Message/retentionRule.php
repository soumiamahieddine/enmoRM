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
namespace bundle\recordsManagement\Message;
/**
 * Class model that represents a retentionRule
 *
 * @package RecordsManagement
 * @author  Prosper DE LAURE (Maarch) <prosper.delaure@maarch.org>
 *
 */
class retentionRule
{
    /**
     * The retention rule code 
     *
     * @var string
     */
    public $code;

    /**
     * The duration of retention 
     *
     * @var duration
     */
    public $duration;

    /**
     * The action to execute when the retention rule is over 
     *
     * @var string
     * @enumeration [preservation, destruction]
     */
    public $finalDisposition;

    /**
     * The description of the rule
     *
     * @var string
     */
    public $description;
    
    /**
     * The label of the rule
     *
     * @var string
     */
    public $label;

    /**
     * Get the string version of message
     * @return string
     */
    public function __toString()
    {
        return $this->code;
    }
    

}