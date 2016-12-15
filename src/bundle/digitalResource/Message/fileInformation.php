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
namespace bundle\digitalResource\Message;

/**
 * Class file information request
 *
 * @package Digitalresource
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class fileInformation
{
    /**
     * The file format
     *
     * @var string
     */
    public $puid;

    /**
     * The file mimetype
     *
     * @var string
     */
    public $mimetype;

    /**
     * The hash in md5
     *
     * @var string
     */
    public $hashMD5;

    /**
     * The hash in md5
     *
     * @var string
     */
    public $hashSHA256;

    /**
     * The hash in md5
     *
     * @var string
     */
    public $hashSHA512;
}
