<?php

namespace models;

/**
 * Class OrderModel
 * @package models
 */
class OrderModel extends Model
{
    /**
     * @var bool
     */
    public $payment = false;

    /**
     * @return array
     */
    public $errors = [];

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'order';
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        if (!is_bool($this->payment)){
            $this->errors['payment'][] = 'Price is not boolean';
        }

        return empty($this->errors);
    }
}