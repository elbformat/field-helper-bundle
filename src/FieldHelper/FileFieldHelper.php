<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\BinaryFile\Value as BinaryValue;

class FileFieldHelper extends AbstractFieldHelper
{
    public static function getName(): string
    {
        return self::class;
    }

    public function getFileName(Content $content, string $fieldName): ?string
    {
        $field = $this->getField($content, $fieldName);

        return $this->getFileFieldValue($field)->fileName;
    }

    protected function getFileFieldValue(Field $field): BinaryValue
    {
        if (null === $field->value) {
            return new BinaryValue();
        }
        if (!$field->value instanceof BinaryValue) {
            throw InvalidFieldTypeException::fromActualAndExpected($field->value, [BinaryValue::class]);
        }

        return $field->value;
    }
}
