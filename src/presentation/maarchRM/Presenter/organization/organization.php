<?php
/*
 * Copyright (C) 2015 Maarch
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
namespace presentation\maarchRM\Presenter\organization;

/**
 * organization serializer
 *
 * @package Organization
 * @author  Maarch Alexandre Morin <alexandre.morin@maarch.org>
 */
class organization
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;
    protected $json;

    /**
     * __construct
     * @param \dependency\json\JsonObject $json
     */
    public function __construct(\dependency\json\JsonObject $json)
    {
        $this->json = $json;
        $this->json->status = true;
    }

    public function byRole($organizations)
    {
        $currentService = \laabs::getToken("ORGANIZATION");

        $res = [];
        foreach ($organizations as $organization) {
            if ($currentService->registrationNumber != $organization->registrationNumber) {
                $res[] = $organization;
            }
        }
        return json_encode($res);
    }
}