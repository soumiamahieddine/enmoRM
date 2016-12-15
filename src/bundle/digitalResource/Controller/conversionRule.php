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

namespace bundle\digitalResource\Controller;

/**
 * Class of conversion rule
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class conversionRule
{

    protected $sdoFactory;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Allow to display all conversion rules
     *
     * @return digitalResource/$conversionRule[]
     */
    public function index()
    {
        $conversionRules = $this->sdoFactory->find('digitalResource/conversionRule');

        return $conversionRules;
    }

    /**
     * New empty conversion rule with default values
     *
     * @return digitalResource/conversionRule The conversion rule object
     */
    public function newConversionRule()
    {
        return \laabs::newInstance("digitalResource/conversionRule");
    }

    /**
     * Edit a conversion rule
     * @param id $conversionRuleId The identifier of conversion rule
     *
     * @return digitalResource/conversionRule conversion rule object
     */
    public function edit($conversionRuleId = null)
    {
        // pre_load values
        if ($conversionRuleId) {
            if (!$this->sdoFactory->exists("digitalResource/conversionRule", $conversionRuleId)) {
                throw \laabs::newException("digitalResource/conversionRuleException", "Conversion rule $conversionRuleId not found.");
            }
            $conversionRule = $this->sdoFactory->read("digitalResource/conversionRule", $conversionRuleId);
        } else {
            $conversionRule = $this->newConversionRule();
        }

        return $conversionRule;
    }

    /**
     * Create a conversion rule
     * @param digitalResource/conversionRule $conversionRule The conversion rule object
     *
     * @return string The identifier of the conversion rule
     */
    public function create($conversionRule)
    {
        $conversionRule->conversionRuleId = \laabs::newId();

        if (!$conversionRule->puid) {
            throw \laabs::newException("digitalResource/conversionRuleException", "Source format is empty");
        }
        if (!$conversionRule->conversionService) {
            throw \laabs::newException("digitalResource/conversionRuleException", "Conversion service is empty");
        }
        if (!$conversionRule->targetPuid) {
            throw \laabs::newException("digitalResource/conversionRuleException", "Target Puid is empty");
        }

        if ($this->sdoFactory->exists("digitalResource/conversionRule", array("puid" => $conversionRule->puid))) {
            throw \laabs::newException("digitalResource/conversionRuleException", "Conversion rule with Puid '$conversionRule->puid' already exist.");
        }

        $this->sdoFactory->create($conversionRule, "digitalResource/conversionRule");

        return $conversionRule->conversionRuleId;
    }

    /**
     * Update a conversion rule
     * @param digitalResource/conversionRule $conversionRule The conversion rule object
     *
     * @return boolean
     */
    public function update($conversionRule)
    {
        $this->sdoFactory->update($conversionRule, "digitalResource/conversionRule");

        return true;
    }

    /**
     * Delete a conversion rule
     * @param digitalResource/conversionRule $conversionRuleId The conversion rule identifier
     *
     * @return boolean
     */
    public function delete($conversionRuleId)
    {
        if (!$conversionRuleId) {
            return false;
        }

        if (!$this->sdoFactory->exists("digitalResource/conversionRule", $conversionRuleId)) {
            throw \laabs::newException("digitalResource/conversionRuleException", "Conversion rule $conversionRuleId not found.");
        }

        $conversionRule = $this->sdoFactory->read("digitalResource/conversionRule", $conversionRuleId);

        return  $this->sdoFactory->delete($conversionRule);
    }
}
