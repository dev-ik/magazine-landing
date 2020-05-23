<?php

namespace interfaces;

/**
 * Interface DbInterface
 * @package interfaces
 */
interface DbInterface
{
    /**
     * @param $tableName
     * @param $id
     * @return mixed
     */
    public function deleteRow($tableName, $id);

    /**
     * @param $query
     * @param string $mode
     * @param array $params
     * @param bool $bindParams
     * @return mixed
     */
    public function query($query, $mode = 'select', $params = [], $bindParams = true);

    /**
     * @return mixed
     */
    public function startTransaction();

    /**
     * @return mixed
     */
    public function endTransaction();

    /**
     * @return mixed
     */
    public function rollBackTransaction();

    /**
     * @param array $config
     * @return DbInterface
     */
    public static function getInstance(array $config): DbInterface;

    /**
     * @param $fetchMode
     * @return mixed
     */
    public function setFetchMode(int $fetchMode);
}