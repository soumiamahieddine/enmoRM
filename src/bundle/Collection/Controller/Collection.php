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
 * Management of Collection
 *
 * @author Jérôme Boucher <jerome.boucher@maarch.org>
 */
class Collection
{
    public $sdoFactory;
    public $userCollectionController;

    /**
     * Constructor of access control class
     *
     * @param \dependency\sdo\Factory $sdoFactory The factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->userCollectionController = \laabs::newController('Collection/UserCollection');
    }

    public function create()
    {
        $collection = \laabs::cast('Collection/Collection');
        $uniqId = \laabs\uniqid();
        $collection->collectionId = $uniqId;
        $collection->name = $uniqId;

        $this->sdoFactory->create($collection, 'Collection/Collection');
        $this->userCollectionController->createByUser($collection->collectionId);

        return $collection;
    }

    // TODO
    // public function createOrgCollection()
    // {

    // }

    /**
     * Update Collection
     *
     * @param  collection/collection $collection Collection Identifier
     *
     * @return collection/collection Updated collection object
     */
    public function update(object $collection = null)
    {
        try {
            $this->readByCollection($collection->collectionId);
        } catch (Exception $e) {
            throw new \core\Exception\NotFoundException("Collection not found");
        }

        return $this->sdoFactory->update($collection, 'Collection/Collection');
    }

    /**
     * Retrieve default stats for screen
     *
     * @param  string                $collectionId Collection Identifier
     *
     * @return collection/collection               Collection object
     */
    public function readByCollection(string $collectionId)
    {
        $collection = $this->sdoFactory->read('Collection/Collection', ['collectionId' => $collectionId]);

        $collection->archiveIds = json_decode($collection->archiveIds);

        return $collection;
    }

    /**
     * Retrieve Collection by User (current user if null)
     *
     * @param  string|null            $accountId  UserAccount Identifer
     *
     * @return collection/collection  $collection User Collection
     */
    public function readByUser(string $accountId = null)
    {
        $userCollection = $this->userCollectionController->readByUser($accountId);

        return $this->readByCollection($userCollection->collectionId);
    }

    public function delete(string $collectionId)
    {
        try {
            $collection = $this->read($collectionId);
        } catch (Exception $e) {
            throw new \core\Exception\NotFoundException("Collection not found");
        }

        try {
            $this->userCollectionController->delete($collection->collectionId);
        } catch (Exception $e) {
            throw new Exception("Error Processing Request");
        }

        return $this->sdoFactory->delete($collection, 'Collection/Collection');
    }
}
