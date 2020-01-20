<?php
/*
 * Copyright (C) 2020 Maarch
 *
 * This file is part of bundle organization.
 *
 * Bundle organization is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle organization is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle organization.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\importExport\Controller;

use core\Exception;

/**
 * Control of the organization
 *
 * @package importExport
 */
class Export
{
    protected $sdoFactory;
    protected $userAccountController;
    protected $userPositionController;
    protected $organizationController;
    protected $roleController;
    protected $descriptionFieldController;
    protected $controller;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory       The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, \dependency\localisation\TranslatorInterface $translator)
    {
        $this->sdoFactory = $sdoFactory;
        $this->userAccountController = \laabs::newController('auth/userAccount');
        $this->serviceAccountController = \laabs::newController('auth/serviceAccount');
        $this->roleController = \laabs::newController('auth/role');
        $this->organizationController = \laabs::newController('organization/organization');
        $this->archivalProfileController = \laabs::newController('recordsManagement/archivalProfile');
        $this->descriptionFieldController = \laabs::newController('recordsManagement/descriptionField');
        $this->retentionRuleController = \laabs::newController('recordsManagement/retentionRule');
        $this->controller = [
            'userAccount' => $this->userAccountController,
            'serviceAccount' => $this->serviceAccountController,
            'role' => $this->roleController,
            'organization' => $this->organizationController,
            'archivalProfile' => $this->archivalProfileController,
            'descriptionField' => $this->descriptionFieldController,
            'retentionRule' => $this->retentionRuleController
        ];
    }

    /**
     * Create a csv file with type of data chosen
     *
     * @param  string $dataType Type of data to export (organization, user, etc)
     *
     * @return binary $csv      Csv files with data exported
     */
    public function create($dataType)
    {
        return true;
    }

    /**
     * Read an excerpt of data type user can export
     *
     * @param  string $dataType Type of data to visualize (organization, user, etc)
     *
     * @action importExport/Export/read
     *
     * @return array $data      Csv files with data exported
     */
    public function read($dataType)
    {
        if (!array_key_exists($dataType, $this->controller)) {
            throw new \core\Exception\BadRequestException("Data your trying to export does not exists");
        }

        $data = $this->controller[$dataType]->index();

        return $data;
    }
}
