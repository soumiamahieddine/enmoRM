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

namespace bundle\Collection\Controller;

/**
 * Management of UserCollection
 *
 * @author Jérôme Boucher <jerome.boucher@maarch.org>
 */
class UserCollection
{
    public $sdoFactory;

    /**
     * Constructor of access control class
     *
     * @param \dependency\sdo\Factory $sdoFactory The factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Create New UserCollection for a user
     *
     * @param  string                    $collectionId Collection Identifier
     *
     * @return collection/userCollection               UserCollection created
     */
    public function createByUser(string $collectionId)
    {
        try {
            $this->read($collectionId);
        } catch (Exception $e) {
            throw new Exception("Collection Already Exists");
        }

        $userCollection = \laabs::cast('Collection/UserCollection');
        $userCollection->collectionId = $collectionId;
        $userCollection->userId = \laabs::getToken("AUTH")->accountId;

        $this->sdoFactory->create($userCollection, 'collection/userCollection');

        return $userCollection;
    }

    /**
     * Read UserCollection
     *
     * @param  string                    $collectionId Collection Identifier
     *
     * @return collection/userCollection               Collection object
     */
    public function read(string $collectionId)
    {
        return $this->sdoFactory->read('Collection/UserCollection', ['$collectionId' => $collectionId]);
    }

    /**
     * Get UserCollection by user provided or current user if not
     *
     * @param  string|null               $accountId User account Identifier
     *
     * @return collection/userCollection            UserCollection Object
     */
    public function readByUser(string $accountId = null)
    {
        if (is_null($accountId)) {
            $accountId = \laabs::getToken("AUTH")->accountId;
        }

        return $this->sdoFactory->read('Collection/UserCollection', ['accountId' => $accountId]);
    }
    /**
     * Delete UserCollection
     *
     * @param  string $collectionId [description]
     * @return [type]               [description]
     */
    public function delete(string $collectionId)
    {
        try {
            $collection = $this->read($collectionId);
        } catch (Exception $e) {
            throw new Exception("Collection Already Exists");
        }

        return $this->sdoFactory->delete($collection, 'Collection/UserCollection');
    }
}
