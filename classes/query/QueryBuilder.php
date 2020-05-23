<?php

namespace classes\query;


class QueryBuilder
{

    /** @var string $sql */
    private $sql = '';

    /**
     * @param Query $query
     * @return string
     */
    public function buildSql(Query $query)
    {
        $this->buildSelect($query->getSelect(), $query->getDistinct());
        $this->buildFrom($query->getTableName());
        $this->buildWhere($query->getWhere());
        $this->buildLimit($query->getLimit());

        return $this->sql;
    }

    /**
     * @param array $selectColumns
     * @param bool $distinct
     */
    private function buildSelect(array $selectColumns, bool $distinct): void
    {
        foreach ($selectColumns as $column => $aliases) {
            $select[] = is_numeric($column) ? $aliases : $column . ' AS ' . $aliases;
        }

        $selectCommand = $distinct ? 'SELECT DISTINCT ' : 'SELECT ';

        $this->sql = $selectCommand . (empty($select) ? '*' : implode(', ', $select));
    }

    /**
     * @param string $tableName
     */
    private function buildFrom(string $tableName): void
    {
        $this->sql .= ' FROM `' . $tableName . '`';
    }

    /**
     * @param array $condition
     */
    private function buildWhere(array $condition): void
    {
        $where = $this->buildCondition($condition);

        $this->sql .= ($where === '' ? '' : ' WHERE ' . $where);

    }

    /**
     * @param $condition
     * @return string
     */
    private function buildCondition($condition): string
    {
        if (is_array($condition)) {
            if (empty($condition)) {
                return '';
            }

            $where = [];

            foreach ($condition as $item => $value) {

                if (is_array($value)) {
                    $values = implode(',', $value);
                    $where[] = $item . ' IN (' . $values . ')';
                    continue;
                } elseif (is_bool($value)) {
                    $value = (int)$value;
                }
                if (is_numeric($item)) {
                    $where[] = $value;
                } else {
                    $where[] = $item . ' = ' . $value;
                }
            }

            return !empty($where) ? implode(' AND ', $where) : '';
        }

        return (string)$condition;
    }

    /**
     * @param int|null $limit
     */
    private function buildLimit(int $limit = null): void
    {
        if ($limit) {
            $this->sql .= ' LIMIT ' . $limit;
        }
    }


    public function insert(string $tableName, array $saveAttributes, array $valueAttributes)
    {
        foreach ($valueAttributes as $value) {
            $values[] = is_string($value) ? "'" . str_replace("'", '"', $value) . "'" : $value;
        }
        return 'INSERT INTO `' . $tableName . '` (`' . implode('`, `', $saveAttributes) . '`) VALUES (' . implode(', ', $values) . ')';

    }

    public function update(string $tableName, int $id, array $saveAttributes, array $valueAttributes)
    {
        foreach ($valueAttributes as $key => $value) {
            $value = is_string($value) ? "'" . str_replace("'", '"', $value) . "'" : $value;
            $values[] = "`" . $saveAttributes[$key] . "` = " . $value;
        }
        return 'UPDATE`' . $tableName . '` SET ' . implode(',', $values) .' WHERE id=' . $id;

    }
}