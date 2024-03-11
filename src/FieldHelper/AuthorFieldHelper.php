<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Core\FieldType\Author\Author;
use Ibexa\Core\FieldType\Author\AuthorCollection;
use Ibexa\Core\FieldType\Author\Value as AuthorValue;

class AuthorFieldHelper extends AbstractFieldHelper
{
    public function getValues(Content $content, string $fieldName): AuthorCollection
    {
        $field = $this->getField($content, $fieldName);

        if (!($field->value instanceof AuthorValue)) {
            throw InvalidFieldTypeException::fromActualAndExpected($field->value, [AuthorValue::class]);
        }

        return $field->value->authors;
    }

    /**
     * This function will return a comma seperated list of all author names.
     */
    public function getNames(Content $content, string $fieldName): string
    {
        $authors = $this->getValues($content, $fieldName);

        $names = [];
        foreach ($authors as $author) {
            $names[] = $author->name;
        }

        return implode(', ', $names);
    }
}
