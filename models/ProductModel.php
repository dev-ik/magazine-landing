<?php

namespace models;

/**
 * Class ProductModel
 * @package models
 */
class ProductModel extends Model
{
    /**
     * @var string
     */
    public $name = null;

    /**
     * @var float
     */
    public $price = 0;

    /**
     * @var bool
     */
    public $active = null;

    /**
     * @var int
     */
    public $category_id = null;

    /**
     * @return array
     */
    public $errors = [];

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'product';
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
            $this->errors['active'][] = 'Active is empty';
        }

        if (empty($this->category_id)){
            $this->errors['category_id'][] = 'CategoryId is empty';
        }

        if (!is_string($this->name)) {
            $this->errors['name'][] = 'Name is not string';
        } elseif (strlen($this->name) > 50) {
            $this->errors['name'][] = 'Max length Name 50 symbols';
        }

        if (!is_bool($this->active)) {
            $this->errors['active'][] = 'Active is not boolean';
        }

        if (!is_numeric($this->price)){
            $this->errors['price'][] = 'Price is not numeric';
        }

        return empty($this->errors);
    }

}