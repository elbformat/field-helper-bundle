<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use EzSystems\EzPlatformMatrixFieldtype\FieldType\Value;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class MatrixFieldHelper extends AbstractFieldHelper
{
    /**
     * Return a row-based 2-dimensional array.
     * [
     *   [A1,B1,C1],
     *   [A2,B2,C2],
     * ]
     *
     * @return string[][]
     *
     * @throws FieldNotFoundException
     * @throws InvalidFieldTypeException
     */
    public function getArray(Content $content, string $fieldName): array
    {
        $rows = $this->getValue($content, $fieldName)->getRows();
        $result = [];
        /** @var Value\Row $row */
        foreach ($rows as $row) {
            /** @var string[] $cells */
            $cells = $row->getCells();
            $result[] = array_values($cells);
        }

        return $result;
    }

    /**
     * Return a key-value based array structure of the table. Each row has the headlines as key.
     * [
     *   [A1 => A2, B1 => B2, C1 => C2],
     *   [A1 => A3, B1 => B3, C1 => C3],
     * ]
     *
     * @return mixed[][]
     *
     * @throws FieldNotFoundException
     * @throws InvalidFieldTypeException
     */
    public function getAssoc(Content $content, string $fieldName): array
    {
        $rows = $this->getArray($content, $fieldName);
        $headlines = $this->getHeadlineIds($content, $fieldName);
        $numHeadlines = count($headlines);

        $result = [];
        /** @var Value\Row $row */
        foreach ($rows as $cells) {
            $rowData = [];
            for ($i = 0; $i < $numHeadlines; $i++) {
                $rowData[$headlines[$i]] = $cells[$i];
            }
            $result[] = $rowData;
        }

        return $result;
    }

    /**
     * Return a key-value list for 2-column tables.
     * [A1 => B1, A2 => B2, A3 => B3]
     *
     * @return mixed[]
     */
    public function getKeyValue(Content $content, string $fieldName, ?string $key = null, ?string $val = null): array
    {
        $rows = $this->getValue($content, $fieldName)->getRows();
        if (null === $key) {
            $key = $this->getHeadlineIds($content, $fieldName)[0] ?? '';
        }
        if (null === $val) {
            $val = $this->getHeadlineIds($content, $fieldName)[1] ?? '';
        }

        $result = [];
        /** @var Value\Row $row */
        foreach ($rows as $row) {
            /** @var string[] $cells */
            $cells = $row->getCells();
            $result[$cells[$key]] = $cells[$val] ?? '';
        }

        return $result;
    }

    /**
     * Return a list for 1-column tables.
     * [A1,A2,A3]
     *
     * @return string[]
     */
    public function getList(Content $content, string $fieldName, string $columnName = null): array
    {
        if (null === $columnName) {
            $columnName = $this->getHeadlineIds($content, $fieldName)[0] ?? '';
        }
        $rows = $this->getValue($content, $fieldName)->getRows();
        $result = [];
        /** @var Value\Row $row */
        foreach ($rows as $row) {
            $cells = $row->getCells();
            $result[] = (string) $cells[$columnName];
        }

        return $result;
    }

    public function isEmpty(Content $content, string $fieldName): bool
    {
        return $this->getValue($content, $fieldName)->getRows()->count() <= 0;
    }

    /**
     * @param array<array<string,string>> $rowsWithCols
     */
    public function updateAssoc(ContentStruct $struct, string $fieldName, array $rowsWithCols, ?Content $content = null): bool
    {
        // No changes
        if (null !== $content) {
            $current = $this->getArray($content, $fieldName);
            if ($this->arrayEquals($current, $rowsWithCols)) {
                return false;
            }
        }

        // Convert to rows
        $rows = [];
        foreach ($rowsWithCols as $cols) {
            $rows[] = new Value\Row($cols);
        }

        $struct->setField($fieldName, $rows);

        return true;
    }

    /**
     * @param string[] $values
     */
    public function updateList(ContentStruct $struct, string $fieldName, array $values, string $columnName, ?Content $content = null): bool
    {
        // No changes
        if (null !== $content) {
            $field = $this->getList($content, $fieldName, $columnName);
            if ($this->isListEqual($field, $values)) {
                return false;
            }
        }
        // Convert to rows
        $rows = [];
        foreach ($values as $value) {
            $rows[] = new Value\Row([$columnName => $value]);
        }

        $struct->setField($fieldName, $rows);

        return true;
    }

    protected function getValue(Content $content, string $fieldName): Value
    {
        $field = $this->getField($content, $fieldName);
        if (!$field->value instanceof Value) {
            throw InvalidFieldTypeException::fromActualAndExpected($field->value, [Value::class]);
        }

        return $field->value;
    }

    /**
     * @param string[] $list1
     * @param string[] $list2
     */
    protected function isListEqual(array $list1, array $list2): bool
    {
        if (\count($list1) !== \count($list2)) {
            return false;
        }
        for ($i = 0, $iMax = \count($list1); $i < $iMax; ++$i) {
            if ($list1[$i] !== $list2[$i]) {
                return false;
            }
        }

        return true;
    }

    /** @return string[] */
    protected function getHeadlineIds(Content $content, string $fieldName): array
    {
        $fieldDef = $content->getContentType()->getFieldDefinition($fieldName);
        if (null === $fieldDef) {
            return [];
        }

        $colIds = [];
        /** @var array[] $columns */
        $columns = $fieldDef->fieldSettings['columns'];
        foreach ($columns as $colDef) {
            $colIds[] = (string) $colDef['identifier'];
        }

        return $colIds;
    }

    protected function arrayEquals(array $arr1, array $arr2): bool
    {
        return $this->arrayContains($arr1, $arr2) && $this->arrayContains($arr2, $arr1);
    }

    protected function arrayContains(array $arr1, array $arr2): bool
    {
        return 1 > count(
            array_udiff($arr1, $arr2, function ($v1, $v2): int {
                if (is_array($v1) && is_array($v2)) {
                    return !$this->arrayContains($v1, $v2) ? 1 : 0;
                }
                if (is_array($v1) || is_array($v2)) {
                    return 1;
                }

                return $v1 !== $v2 ? 1 : 0;
            })
        );
    }
}
