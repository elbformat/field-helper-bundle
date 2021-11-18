<?php
declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\EmailAddress\Value as MailValue;
use Elbformat\FieldHelperBundle\Exception\NotSetException;

/**
 * Helps reading, updating and comparing text field types.
 * https://doc.ezplatform.com/en/latest/api/field_type_reference.
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class TextFieldHelper extends AbstractFieldHelper
{
    public function getString(Content $content, string $fieldName): string
    {
        $value = $this->getOptionalString($content,$fieldName);

        if (empty($value)) {
            throw NotSetException::fromContentAndFieldName($content, $fieldName);
        }

        return $value;
    }

    public function getOptionalString(Content $content, string $fieldName, bool $exceptionOnEmpty = false): ?string
    {
        $field = $this->getField($content, $fieldName);

        return $this->getStringFieldValue($field);
    }

    public function updateString(ContentStruct $struct, string $fieldName, string $value, ?Content $content = null): bool
    {
        // No changes
        if (null !== $content) {
            $field = $this->getField($content, $fieldName);
            if ($this->isStringEqual($field, $value)) {
                return false;
            }
        }
        $struct->setField($fieldName, $value);

        return true;
    }

    protected function isStringEqual(Field $field, ?string $value): bool
    {
        $fieldVal = $this->getStringFieldValue($field);

        return $fieldVal === $value;
    }

    protected function getStringFieldValue(Field $field): ?string
    {
        switch (\get_class($field->value)) {
            case MailValue::class:
                return $field->value->email ?? null;
            default:
                return $field->value->text ?? null;
        }
    }
}
