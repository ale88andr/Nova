<?php

namespace Nova\Core;


use Nova\Core\Exceptions\LogicExceptions\ArgumentError;

abstract class Model
{

    protected static $table;

    private static function sendPdoQuery($sql, $values = [], $write = false)
    {
        $dbh = DatabaseWrapper::getConnection()->query($sql, $values);
        if ($write)
            return $dbh->getRowsCount();
        else
            return $dbh->getResults();
    }

    public static function all($fields = false)
    {
        $sql = 'SELECT ' . self::setFields($fields) . ' FROM ' . static::$table;
        return self::sendPdoQuery($sql);
    }

    /**
     * Find record(s) by constraint.
     * Example: User::find(['login' => $login, 'name' => 'user1'], ['login', 'created_at']);
     *
     * @param string $fields    SQL Where constraint
     * @param array $select     Columns for select(default:false[all table columns])
     * @return mixed
     */
    public static function find($fields, $select = false)
    {
        try {
            if (is_array($fields)) {
                $columns = array_keys($fields);
                $values = array_values($fields);
                $sql = 'SELECT ' . self::setFields($select) . ' FROM ' . static::$table . ' WHERE ';
                foreach ($columns as $index => $column) {
                    if ($index > 0) {
                        $sql .= ' AND ' . $column . ' = ?';
                    } else {
                        $sql .= $column . ' = ?';
                    }
                }

                return self::sendPdoQuery($sql, $values);
            } elseif (is_integer($fields)) {
                return self::fingById($fields, $select);
            } else {
                throw new ArgumentError();
//                echo 'Undefined constraint ' . $fields . ' for find';
            }
        } catch(ArgumentError $e){
            die($e->printTrace());
        }
    }

    public static function findById($id, $fields = false)
    {
        if (is_integer($id)) {
            $sql = 'SELECT ' . self::setFields($fields) . ' FROM ' . static::table . ' WHERE id = ?';
            return static::sendPdoQuery($sql, [$id]);
        }
        else {
            echo 'Can\'t find row by id = ' . $id . ' in "'. static::table . '" table.';
        }
    }

    private static function setFields($fields)
    {
        return $fields != false ? implode(', ', $fields) : ' * ';
    }

    public static function insert($hashValues)
    {
        if(count($hashValues)){
            $columns = array_keys($hashValues);
            $values = '';
            $i = 1;
            foreach ($hashValues as $key => $value) {
                $values .= ":{$key}";
                if($i < count($hashValues))
                    $values .= ', ';

                $i++;
            }

            $sql = 'INSERT INTO ' . static::$table . '(' . implode(', ', $columns) . ') VALUES (' . $values .')';
            return static::sendPdoQuery($sql, $hashValues, true);
        } else {
            echo 'Callerror: insert parameters must be array !';
        }
    }

    public static function update($fields, $hashValues)
    {
        if(is_array($hashValues)){
            $where = array_keys($fields);
            $values = '';
            $i = 1;
            foreach ($hashValues as $key => $value) {
                $values .= "{$key} = :{$key}";
                if($i < count($hashValues))
                    $values .= ', ';

                $i++;
            }

            $sql = 'UPDATE ' . static::$table . ' SET ' . $values . 'WHERE ';
            foreach ($where as $index => $column) {
                if($index > 0){
                    $sql .= " AND {$column} = :{$column}";
                } else {
                    $sql .= "{$column} = :{$column}";
                }
            }

            return static::sendPdoQuery($sql, array_merge($hashValues, $fields), true);
        } else {
            echo 'Callerror: insert parameters must be array !';
        }
    }
} 