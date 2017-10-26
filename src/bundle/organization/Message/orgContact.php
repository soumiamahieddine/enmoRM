<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle organization.
 *
 * Bundle organization is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle organization is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle organization.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\organization\Message;

/**
 * Message of the orgContact
 *
 * @package Organization
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class orgContact
{
    /**
     * The contact identifier
     *
     * @notempty
     * @var id
     */
    public $contactId;

    /**
     * The organization identifier
     *
     * @notempty
     * @var id
     */
    public $orgId;

    /**
     * Is self
     *
     * @var bool
     */
    public $isSelft;
}
