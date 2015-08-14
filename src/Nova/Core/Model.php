<?php

namespace Nova\Core;

use Nova\Core\Exceptions\LogicExceptions\ArgumentError;

abstract class Model
{

    /**
     * Current table
     *
     * @var Model class name
     */
    protected static $table;

    /**
     * A wrapper for Database::query().
     *
     * @param string $sql       The (PDO)SQL instructions
     * @param array $values     Values for PDO statement
     * @param boolean $write    Type of query (READ or WRITE)
     * @return mixed OR int
     */
    private static function sendPdoQuery($sql, $values = [], $write = false)
    {
        $dbh = DatabaseWrapper::getConnection()->query($sql, $values);
        if ($write)
            return $dbh->getRowsCount();
        else
            return $dbh->getResults();
    }

    /**
     * Method gets all records from table.
     * Example: User::all(['login', 'created_at']);
     *
     * @param array $select  Columns for select(default:false[all table columns])
     * @return array
     */
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
     * @throws ArgumentError
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
                return self::findById($fields, $select);
            } else {
                throw new ArgumentError();
            }
        } catch(ArgumentError $e){
            die($e->printTrace());
        }
    }

    /**
     * Find record(s) by id.
     * Example: User::findById(1, ['login', 'created_at']);
     *
     * @param string $id        id constraint
     * @param array $select     Columns for select(default:false[all table columns])
     * @throws ArgumentError
     * @return mixed
     */
    public static function findById($id, $fields = false)
    {
        try {
            if (is_integer($id)) {
                $sql = 'SELECT ' . self::setFields($fields) . ' FROM ' . static::table . ' WHERE id = ?';
                return static::sendPdoQuery($sql, [$id]);
            }
            else {
                throw new ArgumentError();
            }
        } catch(ArgumentError $e) {
            die($e->printTrace());
        }

    }

    /**
     * Return part of query with list of columns or ' * '(all)
     *
     * @param array $select     Columns for select
     * @return string
     */
    private static function setFields($fields)
    {
        return $fields != false ? implode(', ', $fields) : ' * ';
    }

    /**
     * Insert row.
     * Example: User::insert(['login' => $login])
     *
     * @param array $hash_values  Inserted values [column => value, ...]
     * @throws ArgumentError
     * @return int
     */
    public static function insert($hashValues)
    {
        try {
            if(is_array($hashValues)){
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
                throw new ArgumentError();
            }
        } catch (ArgumentError $e){
            die($e->printTrace());
        }

    }

    /**
     * Update row(s).
     * Example: User::update($id, ['login' => $login])
     *
     * @param string $constraint  SQL Where constraint
     * @param array $hash_values  Inserted values [column => value, ...]
     * @throws ArgumentError
     * @return int
     */
    public static function update($fields, $hashValues)
    {
        try {
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
                throw new ArgumentError();
            }
        } catch(ArgumentError $e){
            $e->printTrace();
        }

    }
} 