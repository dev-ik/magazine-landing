<?php

namespace classes;

use interfaces\DbInterface;

/**
 * Class MySql
 * @package classes
 */
class MySql implements DbInterface
{
    /**
     * @var \PDO
     */
    private $dbh;
    /**
     * @var string
     */
    private $query = '';
    /**
     * @var int
     */
    private $error_mode = \PDO::ERRMODE_EXCEPTION;
    /**
     * @var int
     */
    private $fetch_mode = \PDO::FETCH_ASSOC;
    /**
     * @var string
     */
    private $tableName = '';


    /**
     * MySql constructor.
     * @param $dbname
     * @param $dbhost
     * @param $dbuser
     * @param $dbpass
     */
    private function __construct($dbname, $dbhost, $dbuser, $dbpass, $port = '3306')
    {
        try {
            $this->dbh = new \PDO("mysql:name=$dbname;host=$dbhost;port=$port", $dbuser, $dbpass);
            $this->setAttributes();
            $this->dbh->exec("use " . $dbname);
            $this->dbh->exec("set names utf8");
        } catch (\PDOException $ex) {
           throw new \Exception("mysql:name=$dbname;host=$dbhost;port=$port;$dbuser;$dbpass;");
            // throw new \PDOException($ex->getMessage(), $ex);
        }
    }

    /**
     * Start
     */
    public function startTransaction()
    {
        $this->dbh->beginTransaction();
    }

    /***
     * End
     */
    public function endTransaction()
    {
        $this->dbh->commit();
    }

    /**
     * rollBack
     */
    public function rollBackTransaction()
    {
        $this->dbh->rollBack();
    }

    public function deleteRow($tableName, $id)
    {
        $this->query = "DELETE FROM " . $tableName . " WHERE id = " . $id;
        $stmt = $this->dbh->prepare($this->query);
        $stmt->execute();
    }

    /**
     * @param $query
     * @param string $mode
     * @param array $params
     * @return array|bool|mixed
     */
    public function query($query, $mode = 'select', $params = [], $bindParams = true)
    {
        $this->query = $query;
        try {

            $stmt = $this->dbh->prepare($this->query);
            if (!empty($params) && $bindParams) {
                foreach ($params as $key => $value) {
                    if (is_array($value)) {
                        $value = serialize($value);
                        $params[$key] = $value;
                    } elseif (is_bool($value)) {
                        $value = (int)$value;
                        $params[$key] = $value;
                    }

                    $stmt->bindParam($key, $value);
                }
            }
            $result = !empty($params) && !$bindParams ? $stmt->execute($params) : $stmt->execute();

        } catch (\PDOException $ex) {

            throw new \PDOException($ex->getMessage(), $ex);
        }

        $this->clear();

        if ($mode === 'insert') {
            return $this->dbh->lastInsertId();
        }

        return $mode === 'select' ? $stmt->fetchAll() : $result;
    }

    /**
     * @param $query
     * @param array $params
     * @return \PDOStatement
     */
    public function _query($query, $params = [])
    {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * [0] => HY000
     * [1] => 1
     * [2] => near "bogus": syntax error
     * @return array|null
     */
    public function getErrorInfo(): ?array
    {
        return $this->dbh->errorInfo();
    }

    /**
     * setAttributes
     */
    private function setAttributes()
    {
        $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, $this->error_mode);
        $this->dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, $this->fetch_mode);
    }

    /**
     * @param $fetchMode
     */
    public function setFetchMode($fetchMode)
    {
        $this->fetch_mode = $fetchMode;
        $this->dbh->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, $this->fetch_mode);
    }

    /**
     * @return int
     */
    public function getFetchMode()
    {
        return $this->fetch_mode;
    }

    /**
     * Clear
     */
    private function clear()
    {
        $this->query = '';
        $this->tableName = '';
        $this->error_mode = \PDO::ERRMODE_WARNING;
        $this->fetch_mode = \PDO::FETCH_ASSOC;
        $this->setAttributes();
    }

    /**
     * @param $tableName
     */
    private function quoteTableName(string $tableName): void
    {
        $this->tableName = preg_replace('~^"?([a-zA-Z\d_\-]+)"?(?:\s*(\s[a-zA-Z\d]+))?$~', '"$1"$2', trim($tableName));
    }

    /**
     * @param array $config
     * @return DbInterface
     */
    public static function getInstance(array $config): DbInterface
    {
        static $db;
        if (null === $db) {
            $db = new MySql($config['name'], $config['host'], $config['user'], $config['pass'], $config['port']);
        }

        return $db;
    }


}