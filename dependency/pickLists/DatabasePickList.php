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
    protected $sqlSelectStmt;

    /**
     * @var PDOStatement The pdo statement for search
     */
    protected $pdoSearchStmt;

    /**
     * @var PDOStatement The pdo statement for get
     */
    protected $sqlGetStmt;

    /**
     * Constructor
     * @param string $dsn    The database datasource name, includin user and password
     * @param string $table  The source qualified table name
     * @param string $key    The table key expression
     * @param array  $value  The table value expression (sql list of column or expression for select clause)
     * @param string $search The search expression/columns (sql). If omitted, key and value expression used with "like"
     * 
     */
    public function __construct(string $dsn, string $table, string $key, string $value, string $search)
    {
        $this->pdo = new \PDO($dsn);
        $this->pdo->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->table = $table;
        $this->key = $key;
        $this->value = $value;
        $this->search = $search;
    }

    /**
     * Returns a set of values
     * @param string string $query
     *
     * @return array
     */
    public function search(string $query = null, $limit = 100, $offset = 0): array
    {
        $array = [];

        if (is_null($query)) {
            $pdoStmt = $this->pdo->prepare(sprintf('SELECT %s FROM %s OFFSET %d LIMIT %d', $this->value, $this->table, $offset, $limit));
            $params = [];
        } else {
            $pdoStmt = $this->pdo->prepare(sprintf('SELECT %s FROM %s WHERE %s OFFSET %d LIMIT %d', $this->value, $this->table, $this->search, $offset, $limit));
            $params = ['query' => '%'.$query.'%'];
        }

        $pdoStmt->execute($params);
        while ($entry = $pdoStmt->fetchObject()) {
            $array[] = $entry;
        }

        return $array;
    }

    /**
     * Reads an entry or checks existence
     * @param string $key
     *
     * @return string The value or null
     */
    public function get(string $key)
    {
        $pdoStmt = $this->pdo->prepare(sprintf('SELECT %s FROM %s WHERE %s=:key', $this->value, $this->table, $this->key));

        $pdoStmt->execute(['key' => $key]);

        return $pdoStmt->fetchObject();
    }
}
