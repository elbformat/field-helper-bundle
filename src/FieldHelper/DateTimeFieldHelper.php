<?php
declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\Date\Value as DateValue;
use eZ\Publish\Core\FieldType\DateAndTime\Value as DateTimeValue;
use eZ\Publish\Core\FieldType\Time\Value as TimeValue;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class DateTimeFieldHelper extends AbstractFieldHelper
{
    public static function getName(): string
    {
        return self::class;
    }

    /**
     * @throws FieldNotFoundException
     * @throws InvalidFieldTypeException
     */
    public function getDateTime(Content $content, string $fieldName): ?DateTimeInterface
    {
        $field = $this->getField($content, $fieldName);

        return $this->getDateTimeFieldValue($field);
    }

    public function updateDateTime(ContentStruct $struct, string $fieldName, DateTimeInterface $value, ?Content $content=null): bool
    {
        // No changes
        if (null !== $content) {
            $field = $this->getField($content, $fieldName);
            if ($this->isDateTimeEqual($field, $value)) {
                return false;
            }
        }
        // Downgrade to \DateTime
        if ($value instanceof DateTimeImmutable) {
            $value = new DateTime(null,$value->getTimezone());
            $value->setTimestamp($value->getTimestamp());
        }

        $struct->setField($fieldName, $value);

        return true;
    }

    protected function isDateTimeEqual(Field $field, ?DateTimeInterface $value): bool
    {
        $fieldVal = $this->getDateTimeFieldValue($field);
        // Null matches null
        if (null === $fieldVal) {
            return null === $value;
        }
        // Not null set but null given
        if (null === $value) {
            return false;
        }

        return $fieldVal->getTimestamp() === $value->getTimestamp();
    }

    protected function getDateTimeFieldValue(Field $field): ?DateTime
    {
        switch (\get_class($field->value)) {
            case DateValue::class:
                return $field->value->date;
            case DateTimeValue::class:
                return $field->value->value;
            case TimeValue::class:
                if ($field->value->time === null) {
                    return null;
                }

                // first create utc time to get actually saved time
                $t = new DateTime();
                $t->setTimestamp($field->value->time);
                $t->setTimezone(new \DateTimeZone('UTC'));

                try {
                    // return object with correct time and timezone
                    return new \DateTime($t->format('H:i:s'));
                } catch (\Exception $e) {
                    return null;
                }
            default:
                $allowed = [DateValue::class, DateTimeValue::class, TimeValue::class];
                throw InvalidFieldTypeException::fromActualAndExpected($field->value, $allowed);
        }
    }
}
