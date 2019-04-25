<?php
/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\recordsManagement\Controller;

/**
 * Control of the recordsManagement descriptionClass
 *
 * @package recordsManagement
 * @author Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class descriptionPickList
{
    protected $pickLists;

    public function __construct($descriptionPickLists=null)
    {
        $this->pickLists = $descriptionPickLists;
    }

    public function search($name, $query = null)
    {
        $service = $this->getService($name);

        return $service->search($query);
    }

    public function get($name, $key)
    {
        $service = $this->getService($name);

        return $service->get($key);
    }

    protected function getService($name)
    {
        if (!isset($this->pickLists[$name])) {
            return;
        }

        $serviceConf = $this->pickLists[$name];
        $serviceRouter = new \core\Route\ServiceRouter($serviceConf['uri']);
        $serviceDef = $serviceRouter->service;
        $service = $serviceDef->newInstance($serviceConf['parameters']);

        return $service;
    }
}
