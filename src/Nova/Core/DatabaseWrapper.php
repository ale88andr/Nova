<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 14.08.15
 * Time: 12:05
 */

namespace Nova\Core;

use \Nova\Core\Env;
use \PDO;

class DatabaseWrapper
{
    private static $instance = null;

    private $dbh;

    private $query;

    private $isError = false;

    private $results;

    private $count = 0;

    private $dbConnectionHash;

    private $calledClassName = 'stdClass';

    private function __construct()
    {
        $dbConnectionFile = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.yml';
        $this->dbConnectionHash = new Env($dbConnectionFile);

        $dsn = $this->getDsn();
        try {
            $this->dbh = new PDO(
                $dsn,
                $this->dbConnectionHash->get('development.user') ,
                $this->dbConnectionHash->get('development.password')
            );
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Generate a PDO database dsn string.
     *
     * @return string
     */
    private function getDsn()
    {
        switch ($this->dbConnectionHash->get('development.driver')) {
            case 'mysql':
                $dsn = 'mysql:host=' . $this->dbConnectionHash->get('development.host') . ';dbname=' . $this->dbConnectionHash->get('development.database');
                break;

            case 'sqlite':
                $dsn = 'sqlite:' . $this->dbConnectionHash->get('development.database');
                break;

            case 'postgre':
                $dsn = 'pgsql:dbname=' . $this->dbConnectionHash->get('development.database') . ';host=' . $this->dbConnectionHash->get('development.host');
                break;

            default:
                $dsn = null;
        }

        return $dsn;
    }

    /**
     * Create instance of Database(Singltone).
     *
     * @return obj
     * @api
     */
    public static function getConnection()
    {
        if (!isset(self::$instance)) {
            self::$instance = new DatabaseWrapper();
        }

        return self::$instance;
    }

    /**
     * A main PDO wrapper method to sending queries.
     *
     * @param string $sql       The (PDO)SQL instructions
     * @param array $params     Values for PDO statement
     * @param boolean $write    Type of query (READ or WRITE)
     * @return obj
     * @api
     */
    public function query($sql, $params = [], $write = false)
    {
        $this->error = false;
        if ($this->query = $this->dbh->prepare($sql)) {
            if (count($params)) {
                foreach($params as $param => $param_value) {
                    $this->query->bindValue(is_string($param) ? ":{$param}" : ++$param, $param_value);
                }
            }

            try {
                $this->query->execute();
                if (!$write) {
                    $this->results = $this->query->fetchAll(PDO::FETCH_CLASS, $this->calledClassName);
                }

                $this->count = $this->query->rowCount();
            }

            catch(PDOException $e) {
                echo "We have a problem with this request...";
                $this->isError = true;
//                DEVELOPMENT_ENV ? print_r($e->getMessage())
//                    : file_put_contents('error.log', $e->getMessage() , FILE_APPEND);
            }
        }

        return $this;
    }

    /**
     * Access to query results.
     *
     * @return obj
     * @api
     */
    public function getResults()
    {
        switch (count($this->results)) {
            case '0':
                return false;
                break;

            default:
                return $this->results;
                break;
        }
    }

    /**
     * Access to query errors.
     *
     * @return bool
     * @api
     */
    public function isErrors()
    {
        return $this->isError;
    }

    /**
     * Access to query results count.
     *
     * @return int
     * @api
     */
    public function getRowsCount()
    {
        return $this->count;
    }

    public function setCallerClassName($className)
    {
        $this->calledClassName = $className;
    }
} 