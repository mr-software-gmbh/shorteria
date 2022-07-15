<?php

/**
 * This file is part of Shorteria by MR Software GmbH.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace App\Model;

use App\Database\BaseDatabase;

class BaseModel extends BaseDatabase
{
    protected string $query;
    protected string $table;
    protected string $pk;
    protected bool $isJoined = false;
    protected array $attributes = [];
    protected array $preparedConditionValues = [];

    public function __construct($table, $pk)
    {
        parent::__construct();

        $this->table = $table;
        $this->pk = $pk;
    }

    public function setTableAlias($alias)
    {
        if (!empty($alias)) {
            $this->table .= " AS $alias";
        }
    }

    public function find($columns = []): static
    {
        $this->query = 'SELECT ';

        if (is_array($columns) && count($columns)) {
            foreach ($columns as $k => $column) {
                $this->query .= "$column";
                if (count($columns) != ($k + 1)) {
                    $this->query .= ', ';
                }
            }
        } else {
            $this->query .= '*';
        }

        $this->query .= " FROM $this->table ";

        return $this;
    }

    public function where(...$conditions): static
    {
        if (count($conditions)) {
            $this->query .= 'WHERE ';
            $i = 0;
            /***
             * Other logical operators are not yet available. Only AND is working right now since it is the only one
             * needed at the moment
             */
            foreach ($conditions as $data) {
                if (count($data) === 3) {
                    $columnName = $data[0];
                    $operator = $data[1];
                    $preparedColumnName = ':' . $columnName;

                    // Check if the column name param position has BINARY
                    if (str_starts_with($columnName, 'BINARY')) {
                        $preparedColumnName = ':' . str_replace('BINARY ', '', $columnName);
                    }

                    $this->query .= "$columnName $operator $preparedColumnName";
                    $this->preparedConditionValues[$preparedColumnName] = $data[2];
                } else {
                    global $errorMessages;
                    echo $errorMessages['wrongOrmWhereConditionParameter'];
                    throw new \PDOException($errorMessages['wrongOrmWhereConditionParameter']);
                }
                if (count($conditions) != ($i + 1)) {
                    $this->query .= ' AND ';
                }
                $i++;
            }
        }

        return $this;
    }

    public function orderBy($orders = []): static
    {
        if (is_array($orders) && count($orders)) {
            $this->query .= 'ORDER BY ';
            foreach ($orders as $k => $order) {
                $this->query .= "$order ";
                if (count($orders) != ($k + 1)) {
                    $this->query .= ', ';
                }
            }
        }

        return $this;
    }

    public function join($table, $reference1, $reference2, $joinType): static
    {
        $this->isJoined = true;
        $_joinedType = strtoupper($joinType);
        $this->query .= "$_joinedType JOIN $table ON $reference1 = $reference2 ";

        return $this;
    }

    public function limit($int): static
    {
        $this->query .= "LIMIT $int ";

        return $this;
    }

    public function offset($int): static
    {
        $this->query .= "OFFSET $int ";

        return $this;
    }

    public function groupBy($column): static
    {
        $this->query .= "GROUP BY $column ";

        return $this;
    }

    private function joinedRecursive($data): array
    {
        $i = 0;
        $recursive = [];
        foreach ($data as $k => $v) {
            $recursive[$i] = $v;
            foreach ($v as $key => $item) {
                if (strpos($key, '_id')) {
                    // Associated
                    $getAssociatedTableName = explode('_', $key)[0];
                    $this->table = $getAssociatedTableName;
                    $find = $this->find()->where(['id', '=', $item]);
                    $this->isJoined = false;
                    $recursive[$i][$getAssociatedTableName] = $find->execute('single');
                } else {
                    $recursive[$i][$key] = $item;
                }
            }
            $i++;
        }

        return $recursive;
    }

    public function execute($fetchType = 'all')
    {
        // Prepared sql statement started
        $sql = $this->db->prepare($this->query);

        // Bind values to prepared statement
        foreach ($this->preparedConditionValues as $preparedColumnName => $value) {
            $paramType = $value === 'NULL' ? \PDO::PARAM_NULL : \PDO::PARAM_STR;
            $sql->bindValue($preparedColumnName, $value, $paramType);
        }

        // Execute prepared sql statement
        $sql->execute();

        // Set how the fetch mode to be done
        $sql->setFetchMode(\PDO::FETCH_ASSOC);
        $result = $fetchType == 'all' ? $sql->fetchAll() : $sql->fetch();

        if ($this->isJoined) {
            return $this->joinedRecursive($result);
        }

        return $result;
    }

    private function getRequestAttributes($event, $isUpdate = false): array
    {
        $event = (array) $event;
        $values = [];
        foreach ($this->attributes as $attr) {
            if (in_array($attr, array_keys($event))) {
                $val = null;
                if (!is_null($event[$attr])) {
                    $val = htmlspecialchars($event[$attr]);
                }
                $values[$attr] = $val;
            } elseif (!$isUpdate) {
                $values[$attr] = null;
            }

            if (($attr == 'created_at' && !array_key_exists('created_at', $event)) || ($attr == 'modified_at' && !array_key_exists('modified_at', $event))) {
                $values[$attr] = date('Y-m-d H:i:s');
            }
        }

        return $values;
    }

    public function save(): bool|int
    {
        if (in_array($this->pk, array_keys((array) $this))) {
            // Remove attributes that doesn't need to be updated
            foreach ($this->attributes as $attr) {
                if (!in_array($attr, array_keys($this->getRequestAttributes($this, true)))) {
                    $searchIndex = array_search($attr, $this->attributes);
                    unset($this->attributes[$searchIndex]);
                }
            }
            // Combine the model attributes and the values
            $getData = array_combine($this->attributes, $this->getRequestAttributes($this, true));

            // Remove created_at timestamp for update process
            unset($getData['created_at']);

            // Write the UPDATE sql statement
            $query = "UPDATE $this->table SET ";

            // Build the update set statement to be prepared for sql statement
            $i = 0;
            foreach (array_keys($getData) as $columnName) {
                $query .= $columnName . ' = :' . $columnName;
                if (count($getData) != ($i + 1)) {
                    $query .= ', ';
                }
                $i++;
            }

            // Condition to put the
            $query .= " WHERE $this->pk = :id";

            // Prepared sql statement start
            $sql = $this->db->prepare($query);

            // Bind values to prepared values
            foreach ($getData as $columnName => $value) {
                $sql->bindValue(':' . $columnName, $value);
            }
            $sql->bindValue(':id', $this->id);
        } else {
            // Build the insert statement to be prepared for sql statement
            $i = 0;
            $columnNames = '';
            $preparedValues = '';
            foreach ($this->attributes as $columnName) {
                $columnNames .= $columnName;
                $preparedValues .= ':' . $columnName;
                if (count($this->attributes) != ($i + 1)) {
                    $columnNames .= ', ';
                    $preparedValues .= ', ';
                }
                $i++;
            }

            // Write the INSERT statement
            $query = "INSERT INTO $this->table($columnNames) VALUES($preparedValues)";

            // Prepared sql statement start
            $sql = $this->db->prepare($query);

            // Bind values to prepared values
            foreach ($this->getRequestAttributes($this) as $columnName => $value) {
                $sql->bindValue(':' . $columnName, $value);
            }
        }

        return $sql->execute();
    }

    public function delete(): bool|int
    {
        $sql = $this->db->prepare("DELETE FROM $this->table WHERE $this->pk = :id");
        $sql->bindValue(':id', $this->id);

        return $sql->execute();
    }
}
