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
     * Create a new Collection
     *
     * @param  string|null $accountId UserAccount Identifier
     * @param  string|null $orgId     Organization Identifier
     *
     * @return Collection/Collection
     */
    public function create(string $accountId = null, string $orgId = null)
    {
        $collection = \laabs::newInstance('Collection/Collection');
        $uniqId = \laabs\uniqid();
        $collection->collectionId = $uniqId;
        $collection->name = $uniqId;

        if (!is_null($accountId) && !$this->sdoFactory->exists('user/account', ['accountId', $accountId])) {
            throw new \core\Exception\NotFoundException("User not found");
        }
        $collection->accountId = $accountId;

        if (!is_null($orgId) && !$this->sdoFactory->exists('organization/organization', ['orgId', $orgId])) {
            throw new \core\Exception\NotFoundException("Organization not found");
        }
        $collection->orgId = $orgId;

        if (is_null($accountId) && is_null($orgId)) {
            $collection->accountId = \laabs::getToken("AUTH")->accountId;
        }

        $test = $this->sdoFactory->create($collection, 'Collection/Collection');

        return $collection;
    }

    // TODO
    // public function createOrgCollection()
    // {

    // }

    /**
     * Update Collection
     *
     * @param  Collection/Collection $collection Collection Identifier
     *
     * @return Collection/Collection Updated collection object
     */
    public function update(object $collection)
    {
        try {
            $this->readByCollection($collection->collectionId);
        } catch (Exception $e) {
            throw new \core\Exception\NotFoundException("Collection not found");
        }

        $collection = \laabs::cast($collection, 'Collection/Collection');


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
        if (is_null($accountId)) {
            $accountId = \laabs::getToken("AUTH")->accountId;
        }

        if (!$this->sdoFactory->exists('Collection/Collection', ['accountId' => $accountId])) {
            $this->create();
        }

        $collection = $this->sdoFactory->read('Collection/Collection', ['accountId' => $accountId]);
        $collection->archiveIds = json_decode($collection->archiveIds);

        return $collection;
    }

    public function delete(string $collectionId)
    {
        try {
            $collection = $this->read($collectionId);
        } catch (Exception $e) {
            throw new \core\Exception\NotFoundException("Collection not found");
        }

        return $this->sdoFactory->delete($collection, 'Collection/Collection');
    }
}
