<?php

namespace Nova\Core;

use Nova\Core\Exceptions\LogicExceptions\ArgumentError;
use Nova\Helpers\Hash;
use Nova\Interfaces\ModelInterface;

abstract class Model implements ModelInterface
{

    /**
     * Current table
     *
     * @var Model class name
     */
    protected static $table;

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $accessList = [];

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
        $dbh = DatabaseWrapper::getConnection();
        $dbh->setCallerClassName(get_called_class());
        $dbh->query($sql, $values, $write);
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
                $sql = 'SELECT ' . self::setFields($fields) . ' FROM ' . static::$table . ' WHERE id = ?';
                return static::sendPdoQuery($sql, [$id])[0];
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
     * Example: $user = new User();
     *          $user->login = 'user_login';
     *          $user->create();
     *
     * @param array $hash_values  Inserted values [column => value, ...]
     * @throws ArgumentError
     * @return int
     */
    public function create()
    {
        try {
            if(is_array($this->columns)){
                $columns = array_keys($this->columns);
                $values = '';
                $i = 1;
                foreach ($this->columns as $key => $value) {
                    $values .= ":{$key}";
                    if($i < count($this->columns))
                        $values .= ', ';

                    $i++;
                }

                $sql = 'INSERT INTO ' . static::$table . '(' . implode(', ', $columns) . ') VALUES (' . $values .')';
                return static::sendPdoQuery($sql, $this->columns, true);
            } else {
                throw new ArgumentError();
            }
        } catch (ArgumentError $e){
            die($e->printTrace());
        }

    }

    /**
     * Update row.
     * Example: $user = User::findById(5);
     *          $user->email = 'newmail@mail.com';
     *          $user->update();
     *
     * Example: User::update($id, ['login' => $login])
     *
     * @param string $constraint  SQL Where constraint
     * @param array $hash_values  Inserted values [column => value, ...]
     * @throws ArgumentError
     * @return int
     */
    public function update()
    {
        try {
            if(is_array($this->columns)){
                $where[] = 'id';
                $values = '';
                $i = 0;
                $this->permitParams();
                foreach ($this->columns as $key => $value) {
                    $i++;
                    if ('id' == $key){
                        continue;
                    }
                    $values .= "{$key} = :{$key}";
                    if($i < count($this->columns))
                        $values .= ', ';
                }

                $sql = 'UPDATE ' . static::$table . ' SET ' . $values . ' WHERE ';
                foreach ($where as $index => $column) {
                    if($index > 0){
                        $sql .= " AND {$column} = :{$column}";
                    } else {
                        $sql .= "{$column} = :{$column}";
                    }
                }

                return static::sendPdoQuery($sql, $this->columns, true);
            } else {
                throw new ArgumentError();
            }
        } catch(ArgumentError $e){
            $e->printTrace();
        }

    }

    /**
     * Create or update object
     * @return int count of inserted/updated rows
     */
    public function save()
    {
        if (isset($this->id)) {
            $this->update();
        } else {
            $this->create();
        }
    }

    /**
     * Sets permitted parameters
     * Example: $user = User::findById(1);
     *          $user->permit(['email']);
     *          $user->email = 'newmail@inbox.ru';
     *          $user->login = 'blah';
     *          $user->update(); //Update only email!
     *
     * @param array $accessList
     */
    public function permit($accessList = [])
    {
        $this->accessList = $accessList;
    }

    /**
     * Handle input parameters ($this->columns) by access list ($this->accessList)
     */
    private function permitParams()
    {
        foreach($this->columns as $key => $value)
        {
            if(!in_array($key, $this->accessList)){
                if ($key != 'id'){
                    Hash::remove($this->columns, $key);
                }
            }
        }
    }

    /**
     * Model table name
     * @return string
     */
    public function getCurrentTable()
    {
        return static::$table;
    }

    public function __set($key, $value)
    {
        Hash::set($this->columns, $key, $value);
    }

    public function __get($key)
    {
        return Hash::get($this->columns, $key);
    }

    public function __isset($key)
    {
        return Hash::keyExists($this->columns, $key);
    }
} 