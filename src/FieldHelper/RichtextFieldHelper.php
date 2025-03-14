<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\Helper\FieldHelper;
use Ibexa\FieldTypeRichText\FieldType\RichText\Value;
use Ibexa\Contracts\FieldTypeRichText\RichText\Converter as RichtextConverter;

/**
 * Helps reading, updating and comparing richtext field types.
 * https://doc.ibexa.co/en/latest/content_management/field_types/field_type_reference/field_type_reference/
 *
 * @author Ronny Gericke <ronny.gericke@elbformat.de>
 */
class RichtextFieldHelper extends AbstractFieldHelper
{
    public function __construct(
        protected FieldHelper $fieldHelper,
        protected RichtextConverter $richtextConverter
    ) {
    }

    public static function getName(): string
    {
        return self::class;
    }

    public function getXml(Content $content, string $fieldName): ?\DOMDocument
    {
        $field = $this->getField($content, $fieldName);

        return $this->getXmlFieldValue($field);
    }

    public function getHtml(Content $content, string $fieldName): string
    {
        $xml = $this->getXml($content, $fieldName);
        if (null === $xml) {
            return '';
        }

        return trim($this->richtextConverter->convert($xml)->saveHTML() ?: '');
    }

    public function isEmpty(Content $content, string $fieldName): bool
    {
        $field = $content->getField($fieldName);
        if (null === $field) {
            throw FieldNotFoundException::fromContentAndField($content, $fieldName);
        }
        return $this->fieldHelper->isFieldEmpty($content, $fieldName);
    }

    public function updateXml(ContentStruct $struct, string $fieldName, ?\DOMDocument $value, ?Content $content): bool
    {
        // No changes
        if (null !== $content) {
            $field = $this->getField($content, $fieldName);
            if ($this->isXmlEqual($field, $value)) {
                return false;
            }
        }
        $struct->setField($fieldName, $value);

        return true;
    }

    protected function isXmlEqual(Field $field, ?\DOMDocument $value): bool
    {
        if ($value === null) {
            return false;
        }

        $fieldVal = $this->getXmlFieldValue($field);
        return $fieldVal->saveXML() === $value->saveXML();
    }

    protected function getXmlFieldValue(Field $field): \DOMDocument
    {
        /** @var Value $fieldValue */
        $fieldValue = $field->value;

        return $fieldValue->xml;
    }
}
