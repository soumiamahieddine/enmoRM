<?php

/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of dependency pickLists.
 *
 * Dependency pickLists is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency pickLists is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with dependency pickLists. If not, see <http://www.gnu.org/licenses/>.
 */

namespace dependency\pickLists;

/**
 * Pick lists from database
 *
 * @author Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class DatabasePickList implements PickListInterface
{
    /**
     * @var PDO The pdo object
     */
    protected $pdo;

    /**
     * @var PDOStatement The pdo statement for entire set
     */
    protected $pdoListStmt;

    /**
     * @var PDOStatement The pdo statement for search
     */
    protected $pdoSearchStmt;

    /**
     * @var PDOStatement The pdo statement for get
     */
    protected $pdoGetStmt;

    /**
     * Constructor
     * @param string $dsn    The database datasource name, includin user and password
     * @param string $table  The source qualified table name
     * @param string $key    The table key column name
     * @param string $value  The table value expression (sql)
     * @param string $search The search expression/columns (sql). If omitted, key and value expression used with "like" 
     */
    public function __construct(string $dsn, string $table, string $key, string $value, string $search=null)
    {
        $this->pdo = new \PDO($dsn);
        $this->pdo->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

         if (is_null($search)) {
            $search = $key." || ' ' || ".$value;
        }

        $this->pdoGetStmt = $this->pdo->prepare(sprintf('SELECT %s "value" FROM %s WHERE %s=:key', $value, $table, $key));
        $this->pdoListStmt = $this->pdo->prepare(sprintf('SELECT %s "key", %s "value" FROM %s', $key, $value, $table));
        $this->pdoSearchStmt = $this->pdo->prepare(sprintf('SELECT %s "key", %s "value" FROM %s WHERE %s LIKE :query', $key, $value, $table, $search));
    }

    /**
     * Returns a set of values
     * @param string string $query
     *
     * @return array
     */
    public function search(string $query = null): array
    {
        $array = [];

        if (is_null($query)) {
            $pdoStmt = $this->pdoListStmt;
            $params = [];
        } else {
            $pdoStmt = $this->pdoSearchStmt;
            $params = ['query' => '%'.$query.'%'];
        }

        $pdoStmt->execute($params);
        while ($entry = $pdoStmt->fetch(\PDO::FETCH_ASSOC)) {
            $array[$entry['key']] = $entry['value'];
        }

        return $array;
    }

    /**
     * Reads an entry or checks existence
     * @param string $key
     *
     * @return string The value or null
     */
    public function get(string $key): string
    {
        $this->pdoGetStmt->execute(['key' => $key]);

        return $this->pdoGetStmt->fetchColumn();
    }
}
