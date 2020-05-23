<?php

namespace models;

/**
 * Class CategoryModel
 * @package models
 */
class CategoryModel extends Model
{
    /**
     * @var string
     */
    public $name = null;

    /**
     * @var bool
     */
    public $active = null;

    /**
     * @return array
     */
    public $errors = [];

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'category';
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        if (empty($this->name)) {
            $this->errors['name'][] = 'Name is empty';
        }

        if (empty($this->active)) {
            $this->errors['name'][] = 'Active is empty';
        }

        if (!is_string($this->name)) {
            $this->errors['name'][] = 'Name is not string';
        } elseif (strlen($this->name) > 50) {
            $this->errors['name'][] = 'Max length Name 50 symbols';
        }

        if (!is_bool($this->active)) {
            $this->errors['active'][] = 'Active is not boolean';
        }

        return empty($this->errors);
    }

}