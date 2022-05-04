<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle medona.
 *
 * Bundle medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\medona\Model;

/**
 * Class model that represents a message identifier
 *
 * @package RecordsManagement
 * @author Alexis RAGOT <alexis.ragot@maarch.org>
 *
 * @key [messageId, objectClass, objectId]
 * @fkey [messageId] medona/message [messageId]
 */
class unitIdentifier
{
    /**
     * The message identifier
     *
     * @var id
     */
    public $messageId;

    /**
     * The object class
     *
     * @var string
     */
    public $objectClass;

    /**
     * The object identifier
     *
     * @var id
     */
    public $objectId;
}
