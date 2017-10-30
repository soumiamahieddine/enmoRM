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

namespace bundle\digitalResource\Message;

/**
 * Class model that represents a conversion rule
 *
 * @package Digitalresource
 * @author Alexis RAGOT <alexis.ragot@maarch.org>
 */
class conversionRule
{
    /**
     * The conversion rule identifier
     *
     * @var id
     */
    public $conversionRuleId;

    /**
     * The puid
     *
     * @var string
     * @notempty
     */
    public $puid;

    /**
     * The conversion service
     *
     * @var string
     * @notempty
     */
    public $conversionService;

    /**
     * The target Puid
     *
     * @var string
     * @notempty
     */
    public $targetPuid;
}
