<?php

namespace models;

use classes\Application;
use classes\query\Query;
use classes\query\QueryBuilder;
use interfaces\DbInterface;
use interfaces\ModelInterface;

/**
 * Class Model
 * @package models
 */
abstract class Model implements ModelInterface
{
    /**
     * @var int|null
     */
    protected $id = null;

    /**
     * @var DbInterface
     */
    protected $db;

    /**
     * @var array
     */
    protected $errors = [];

    /** @var array ExcludeProperties */
    private const ExcludeProperties = ['db', 'errors', 'id', 'updated_at', 'created_at'];


    /**
     * Model constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $app = Application::getInstance();
        $this->setDb($app->getDb());
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function tableName(): string
    {
        return static::tableName();
    }

    /**
     * @return Query
     * @throws \Exception
     */
    public static function find(): Query
    {
        return new Query(static::tableName(), static::class);
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public static function delete($id): void
    {
        $model = self::find()->where(['id' => $id])->one();

        if (!$model) {
            throw new \Exception(__CLASS__ . ' with id:' . $id . ' not found');
        }

        $app = Application::getInstance();
        $app->getDb()->deleteRow(static::tableName(), $id);
    }

    /**
     * @param mixed $db
     */
    private function setDb($db): void
    {
        $this->db = $db;
    }

    /**
     * @return bool
     */
    public function isNewRecord(): bool
    {
        return (bool)($this->getId() === null);
    }

    /**
     * @param bool $validate
     * @param array $attributes
     * @return int|null
     */
    public function save(bool $validate = true, array $attributes = []): ?int
    {
        if ($validate) {
            if (!$this->validate()) {
                return null;
            }
        }

        $tableName = static::tableName();

        if (!empty($attributes)) {
            foreach (get_object_vars($this) as $property => $defaultValue) {
                if (in_array($property, $attributes) ||
                    ($property === 'id' && !$this->isNewRecord())
                ) {
                    $saveAttributes[] = $property;
                    if (is_bool($this->$property)) {
                        $this->$property = (int)$this->$property;
                    } elseif (is_array($this->$property)) {
                        $this->$property = json_encode($this->$property);
                    }
                    $valueAttributes[] = $this->$property;
                }
            }
        } else {
            foreach (get_object_vars($this) as $property => $value) {
                if (!in_array($property, self::ExcludeProperties) ||
                    ($property === 'id' && !$this->isNewRecord())
                ) {
                    if (is_bool($this->$property)) {
                        $this->$property = (int)$this->$property;
                    } elseif (is_array($this->$property)) {
                        $this->$property = json_encode($this->$property);
                    }
                    $saveAttributes[] = $property;
                    $valueAttributes[] = $this->$property;
                }

            }
        }

        $sql = $this->isNewRecord() ? (new QueryBuilder())->insert($tableName, $saveAttributes, $valueAttributes)
            : (new QueryBuilder())->update($tableName, $this->getId(), $saveAttributes, $valueAttributes);

        return $this->db->query($sql, $this->isNewRecord() ? 'insert' : 'update');
    }
}