<?php

/*
 * Copyright (C) 2021 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\UserStory\app;

/**
 * Interface for collections
 */
interface CollectionInterface
{
    /**
     * Display Collection page
     *
     * @uses   Collection/Collection/readByUser
     * @return Collection/Collection/index
     *
     */
    public function readCollection();

    /**
     * Update Collection
     *
     * @param  Collection/Collection $collection Collection Object
     *
     * @uses   Collection/Collection/update
     *
     */
    public function updateCollection(object $collection);
}
