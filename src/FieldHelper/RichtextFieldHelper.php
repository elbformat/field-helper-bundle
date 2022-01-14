<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\Helper\FieldHelper;
use EzSystems\EzPlatformRichText\eZ\FieldType\RichText\Value;

/**
 * Helps reading, updating and comparing richtext field types.
 * https://doc.ezplatform.com/en/latest/api/field_type_reference.
 *
 * @author Ronny Gericke <ronny.gericke@elbformat.de>
 */
class RichtextFieldHelper extends AbstractFieldHelper
{
    protected FieldHelper $fieldHelper;

    public function __construct(FieldHelper $fieldHelper)
    {
        $this->fieldHelper = $fieldHelper;
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

    public function isEmpty(Content $content, string $fieldName): bool
    {
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
