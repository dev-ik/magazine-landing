<?php


namespace classes\query;


use classes\Application;
use interfaces\DbInterface;
use models\Model;

class Query
{
    const LEFT_JOIN = 'LEFT JOIN';
    const RIGHT_JOIN = 'RIGHT JOIN';
    const INNER_JOIN = 'INNER JOIN';

    /**
     * @var string $tableName
     */
    private $tableName;

    /**
     * @var string $className
     */
    private $className;

    /**
     * @var array $select
     */
    private $select = [];

    /**
     * @var array $where
     */
    private $where = [];

    /**
     * @var array $params
     */
    private $params = [];


    /**
     * @var bool $distinct
     */
    private $distinct = false;

    /**
     * @var null|int $limit
     */
    private $limit = null;

    /**
     * @var DbInterface
     */
    private $db;

    /**
     * Query constructor.
     * @param string $tableName
     * @throws \Exception
     */
    public function __construct(string $tableName, string $className)
    {
        $this->tableName = $tableName;
        $this->className = $className;
        $app = Application::getInstance();
        $this->db = $app->getDb();
    }

    /**
     * @param array $columns
     * @return Query
     */
    public function select(array $columns = []): Query
    {
        $this->select = $columns;
        return $this;
    }

    /**
     * @param $condition
     * @param array $params
     * @param bool $bindParams
     * @return Query
     */
    public function where($condition, $params = []): Query
    {
        $this->where = $condition;
        $this->addParams($params);
        return $this;
    }

    public function distinct(): void
    {
        $this->distinct = true;
    }

    /**
     * @return mixed
     */
    public function all()
    {
        $sql = (new QueryBuilder())->buildSql($this);
        return $this->db->query($sql,'select', $this->params);
    }


    /**
     * @return mixed
     */
    public function one(): ?Model
    {
        $this->limit = 1;

        $this->db->setFetchMode(\PDO::FETCH_OBJ);
        $resultQuery =  $this->all();

        if (!empty($resultQuery[0])) {
            /** @var Model $model */
            $model = new $this->className();
            $resultQuery = reset($resultQuery);
            foreach ($resultQuery as $property => $value){
                $property === 'id' ? $model->setId($value) : $model->$property = $value ;
            };

            return $model;
        }


        return null;
    }

    /**
     * @param $params
     */
    public function addParams($params)
    {
        $this->params[] = '';
        if (!empty($params)) {
            if (empty($this->params)) {
                $this->params = array_merge($this->params, $params);
            } else {
                foreach ($params as $name => $value) {
                    if (is_int($name)) {
                        $this->params[] = $value;
                    } else {
                        $this->params[$name] = $value;
                    }
                }
            }
        }
        unset($this->params[0]);
    }

    /**
     * @param int $count
     * @return Query
     */
    public function limit(int $count): Query
    {
        $this->limit = $count;
        return $this;
    }

    /**
     * @return array
     */
    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return bool
     */
    public function getDistinct(): bool
    {
        return $this->distinct;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

}