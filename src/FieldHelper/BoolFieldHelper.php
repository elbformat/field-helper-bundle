<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\Checkbox\Value as CheckboxValue;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class BoolFieldHelper extends AbstractFieldHelper
{
    public static function getName(): string
    {
        return self::class;
    }

    /**
     * @throws FieldNotFoundException
     * @throws InvalidFieldTypeException
     */
    public function getBool(Content $content, string $fieldName): ?bool
    {
        $field = $this->getField($content, $fieldName);

        return $this->getBooleanFieldValue($field);
    }

    public function updateBool(ContentStruct $struct, string $fieldName, ?bool $value, ?Content $content = null): bool
    {
        // No changes
        if (null !== $content) {
            $field = $this->getField($content, $fieldName);
            if ($this->isBooleanEqual($field, $value)) {
                return false;
            }
        }
        $struct->setField($fieldName, $value);

        return true;
    }

    protected function isBooleanEqual(Field $field, ?bool $value): bool
    {
        $fieldVal = $this->getBooleanFieldValue($field);

        return $fieldVal === $value;
    }

    protected function getBooleanFieldValue(Field $field): ?bool
    {
        if (!$field->value instanceof CheckboxValue) {
            throw InvalidFieldTypeException::fromActualAndExpected($field->value, [CheckboxValue::class]);
        }

        return $field->value->bool;
    }
}
