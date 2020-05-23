<?php


namespace interfaces;

use classes\query\Query;

/**
 * Interface ModelInterface
 * @package interfaces
 */
interface ModelInterface
{
    public function validate(): bool;

    public function save(bool $validate = true, array $attributes = []): ?int;

    public function getErrors(): array;

    public static function find(): Query;

    public static function delete(int $id): void;

}