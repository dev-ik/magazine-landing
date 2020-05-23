<?php


namespace models;


class UserModel extends Model
{
    /**
     * @var int
     */
    public $id = 1;

    /**
     * @var string
     */
    public $name = 'admin';

    public function validate(): bool
    {
        return true;
    }

    public function getUser(): array
    {
        return [
          'id' => $this->id,
          'name' => $this->name
        ];
    }

}