<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle digitalResource.
 *
 * Bundle digitalResource is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle digitalResource is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\digitalResource\Model;
/**
 * Class model that represents a digital resource packed item 
 *
 * @package Digitalresource
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 * 
 * @fkey [packageId] digitalResource/package [packageId]
 * @fkey [resId] digitalResource/digitalResource [resId]
 * 
 */
class packedResource
{
    /**
     * The package identifier
     *
     * @var string
     */
    public $packageId;

    /**
     * The id of a digital resource contained 
     *
     * @var id
     */
    public $resId;

    /**
     * The name of the digital resource on the package
     *
     * @var string
     */
    public $name;

} // END class 
