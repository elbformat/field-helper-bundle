<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType\Selection\Value as SelectionValue;

class SelectionFieldHelper extends AbstractFieldHelper
{
    public function getValue(Content $content, string $fieldName): ?int
    {
        $selections = $this->getMultipleValues($content, $fieldName);

        $firstOption = reset($selections);
        if (false === $firstOption) {
            return null;
        }

        return $firstOption;
    }

    /**
     * @return int[]
     */
    public function getMultipleValues(Content $content, string $fieldName): array
    {
        $field = $this->getField($content, $fieldName);

        if (!($field->value instanceof SelectionValue)) {
            throw InvalidFieldTypeException::fromActualAndExpected($field->value, [SelectionValue::class]);
        }

        $selections = $field->value->selection;

        if (empty($selections)) {
            return [];
        }

        return $selections;
    }

    public function getSelectionName(Content $content, string $fieldName): ?string
    {
        $index = $this->getValue($content, $fieldName);
        if (null === $index) {
            return null;
        }
        $fieldDef = $content->getContentType()->getFieldDefinition($fieldName);
        /** @var array{options:string[]} $settings */
        $settings = $fieldDef?->getFieldSettings() ?? [];

        return $settings['options'][$index] ?? null;
    }
}
