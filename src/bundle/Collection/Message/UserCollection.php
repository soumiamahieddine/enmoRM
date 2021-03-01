<?php

/*
 * Copyright (C) 2021 Maarch
 *
 * This file is part of bundle collection.
 *
 * Bundle collection is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle collection is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle collection.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\Collection\Message;

/**
 * userCollection message
 *
 * @package Collection
 * @author  Jerome Boucher <jerome.boucher@maarch.org>
 */
class UserCollection
{
    /**
     * The user account identifier
     *
     * @var id
     *
     */
    public $accountId;

    /**
     * The user account identifier
     *
     * @var id
     * @notempty
     */
    public $collectionId;

    /**
     * The user account identifier
     *
     * @var id
     *
     */
    public $orgId;
}
