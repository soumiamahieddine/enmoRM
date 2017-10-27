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
namespace bundle\digitalResource;

/**
 * API admin formats of digital resource
 */
interface contentTypeInterface
{
    /**
     * List content types
     *
     * @action digitalResource/contentType/index
     */
    public function read();

    /**
     * Create the content type
     * @param digitalResource/contentType $contentType
     *
     * @action digitalResource/contentType/create
     */
    public function create($contentType);

    /**
     * Get the content type
     *
     * @action digitalResource/contentType/get
     */
    public function read_name_();

    /**
     * Update the content type
     * @param digitalResource/contentType $contentType
     *
     * @action digitalResource/contentType/update
     */
    public function update_name_($contentType);
}
