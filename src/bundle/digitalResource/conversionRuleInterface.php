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
 * API admin conversion rule
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface conversionRuleInterface {

    /**
     * Allow to display all conversion rule
     *
     * @action digitalResource/conversionRule/index
     */
    public function readList();

    /**
     * Edit a conversion rule
     *
     * @action digitalResource/conversionRule/edit
     */
    public function read_conversionRuleId_();

    /**
     * Create a new conversion rule
     * @param digitalResource/conversionRule $conversionRule The conversion rule object
     *
     * @action digitalResource/conversionRule/create
     */
    public function create($conversionRule);

    /**
     * Update an existing conversion rule
     * @param digitalResource/conversionRule $conversionRule The conversion rule object
     *
     * @action digitalResource/conversionRule/update
     */
    public function update($conversionRule);

    /**
     * Delete a conversion rule
     *
     * @action digitalResource/conversionRule/delete
     */
    public function delete_conversionRuleId_();
}
