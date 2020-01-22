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
class ImportExport
{
    protected $sdoFactory;
    protected $userAccountController;
    protected $serviceAccountController;
    protected $userPositionController;
    protected $organizationController;
    protected $archivalProfileController;
    protected $roleController;
    protected $descriptionFieldController;
    protected $retentionRuleController;
    protected $controller;
    protected $model;
    protected $limit;

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
            'useraccount' => $this->userAccountController,
            'serviceaccount' => $this->serviceAccountController,
            'organization' => $this->organizationController,
            'archivalprofile' => $this->archivalProfileController,
            'descriptionfield' => $this->descriptionFieldController,
            'retentionrule' => $this->retentionRuleController
        ];

        $this->message = [
            'useraccount' => 'auth/userAccountImportExport',
            'serviceaccount' => 'auth/serviceAccountImportExport',
            'organization' => 'organization/organization',
            'archivalprofile' => 'recordsManagement/archivalProfile',
            'descriptionfield' => 'recordsManagement/descriptionField',
            'retentionrule' => 'recordsManagement/retentionRule'
        ];

        $this->limit =  \laabs::configuration('presentation.maarchRM')['maxResults'];
    }

    public function getDefaultHeader($dataType)
    {
        $object = \laabs::newMessage($this->message[strtolower($dataType)]);
        foreach ($object as $key => $value) {
            $header[] = $key;
        }

        return $header;
    }
}
