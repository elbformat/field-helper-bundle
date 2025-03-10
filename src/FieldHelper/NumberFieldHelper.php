<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\Float\Value as FloatValue;
use Ibexa\Core\FieldType\Integer\Value as IntValue;

/**
 * Helps reading, updating and comparing numeric field types.
 * https://doc.ibexa.co/en/latest/content_management/field_types/field_type_reference/field_type_reference/
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class NumberFieldHelper extends AbstractFieldHelper
{
    public static function getName(): string
    {
        return self::class;
    }

    /**
     * @throws FieldNotFoundException
     * @throws InvalidFieldTypeException
     */
    public function getInteger(Content $content, string $fieldName): ?int
    {
        $field = $this->getField($content, $fieldName);

        return $this->getIntegerFieldValue($field);
    }

    /**
     * @throws FieldNotFoundException
     * @throws InvalidFieldTypeException
     */
    public function getFloat(Content $content, string $fieldName): ?float
    {
        $field = $this->getField($content, $fieldName);

        return $this->getFloatFieldValue($field);
    }

    /**
     * @throws FieldNotFoundException
     * @throws InvalidFieldTypeException
     */
    public function updateInteger(ContentStruct $struct, string $fieldName, int $value, ?Content $content = null): bool
    {
        // No changes
        if (null !== $content) {
            $field = $this->getField($content, $fieldName);
            if ($this->getIntegerFieldValue($field) === $value) {
                return false;
            }
        }
        $struct->setField($fieldName, $value);

        return true;
    }

    /**
     * @throws FieldNotFoundException
     * @throws InvalidFieldTypeException
     */
    public function updateFloat(ContentStruct $struct, string $fieldName, float $value, ?Content $content = null): bool
    {
        // No changes
        if (null !== $content) {
            $field = $this->getField($content, $fieldName);
            if ($this->getFloatFieldValue($field) === $value) {
                return false;
            }
        }
        $struct->setField($fieldName, $value);

        return true;
    }

    /**
     * @throws InvalidFieldTypeException
     */
    protected function getIntegerFieldValue(Field $field): ?int
    {
        switch (true) {
            case $field->value instanceof  IntValue:
                return $field->value->value;
            case $field->value instanceof FloatValue:
                return null === $field->value->value ? null : (int) round($field->value->value);
            default:
                $allowed = [IntValue::class, FloatValue::class];
                throw InvalidFieldTypeException::fromActualAndExpected($field->value, $allowed);
        }
    }

    /**
     * @throws InvalidFieldTypeException
     */
    protected function getFloatFieldValue(Field $field): ?float
    {
        switch (true) {
            case $field->value instanceof FloatValue:
                return $field->value->value;
            case $field->value instanceof  IntValue:
                return null === $field->value->value ? null : (float) $field->value->value;
            default:
                $allowed = [FloatValue::class, IntValue::class];
                throw InvalidFieldTypeException::fromActualAndExpected($field->value, $allowed);
        }
    }
}
