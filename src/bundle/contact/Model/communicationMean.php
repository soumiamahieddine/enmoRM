<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle contact.
 *
 * Bundle contact is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle contact is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle contact.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\contact\Model;
/**
 * Communication means 
 *
 * @package Contact
 *
 * @pkey [code]
 * @key [name]
 */
class communicationMean
{
    /**
     * The communication mean code
     *
     * @var string
     */
    public $code;    

    /**
     * The communication mean name
     *
     * @var string
     */
    public $name;    

    /**
     * The communication mean status [active or not]
     *
     * @var boolean
     */
    public $enabled = true;  
}