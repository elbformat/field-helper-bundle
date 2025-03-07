<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use Elbformat\FieldHelperBundle\Data\Url;
use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\Url\Value as UrlValue;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class UrlFieldHelper extends AbstractFieldHelper
{
    public static function getName(): string
    {
        return self::class;
    }

    /**
     * @throws FieldNotFoundException
     * @throws InvalidFieldTypeException
     */
    public function getUrl(Content $content, string $fieldName): Url
    {
        $field = $this->getField($content, $fieldName);

        $value = $this->getUrlFieldValue($field);

        return Url::fromUrlValue($value);
    }

    public function updateUrl(ContentStruct $struct, string $fieldName, ?string $url, ?string $text, ?Content $content = null): bool
    {
        // No changes
        $newUrl = new UrlValue($url, $text);
        if (null !== $content) {
            $field = $this->getField($content, $fieldName);
            $value = $this->getUrlFieldValue($field);
            if ($this->urlEquals($value, $newUrl)) {
                return false;
            }
        }
        $struct->setField($fieldName, $newUrl);

        return true;
    }

    protected function urlEquals(UrlValue $url1, UrlValue $url2): bool
    {
        return ($url1->text === $url2->text && $url1->link === $url2->link);
    }

    protected function getUrlFieldValue(Field $field): UrlValue
    {
        if (null === $field->value) {
            return new UrlValue();
        }
        if (!$field->value instanceof UrlValue) {
            throw InvalidFieldTypeException::fromActualAndExpected($field->value, [UrlValue::class]);
        }

        return $field->value;
    }
}
