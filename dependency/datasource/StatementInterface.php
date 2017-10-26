<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency datasource.
 *
 * Dependency datasource is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency datasource is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency datasource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\datasource;
interface StatementInterface
{
    public function bindParam($parameter, &$variable, $data_type, $length);
    public function bindValue($parameter, $value, $data_type);
    public function getError();
    public function getQueryString();
    public function execute($parameters);
    public function getParams();
}