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
 * Model of the contactPosition
 *
 * @package Organization
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class contactPositionTree
{
    /**
     * The contactId
     *
     * @var id
     */
    public $contactId;

    /**
     * The user dislay name
     *
     * @var string
     */
    public $displayName;

    /**
     * The organization's identitfier
     *
     * @var id
     */
    public $orgId;

    /**
     * Is self contact
     *
     * @var boolean
     */
    public $isSelf;
}
