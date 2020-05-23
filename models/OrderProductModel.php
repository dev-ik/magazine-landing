<?php

namespace models;

/**
 * Class OrderProductModel
 * @package models
 */
class OrderProductModel extends Model
{
    /**
     * @var int
     */
    public $product_id = null;

    /**
     * @var int
     */
    public $order_id = null;

    /**
     * @return array
     */
    public $errors = [];

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'order_product';
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function validate(): bool
    {
        if (empty($this->product_id)) {
            $this->errors['product_id'][] = 'Product ID is required';
        }

        if (!is_int($this->product_id)){
            $this->errors['product_id'][] = 'Product id must be {int}';
        } else if (!ProductModel::find()->where(['id' => $this->product_id])->one()) {
            $this->errors['product_id'][] = 'Product with ID not exists';
        }

        if (empty($this->order_id)) {
            $this->errors['order_id'][] =  'Order ID is required';
        }

        if (!is_int($this->order_id)){
            $this->errors['order_id'][] = 'Order id must be {int}';
        } else if (!OrderModel::find()->where(['id' => $this->order_id])->one()) {
            $this->errors['product_id'][] = 'Product with ID not exists';
        }

        return empty($this->errors);
    }
}