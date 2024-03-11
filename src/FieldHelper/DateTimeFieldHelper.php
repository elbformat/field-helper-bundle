<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\Date\Value as DateValue;
use Ibexa\Core\FieldType\DateAndTime\Value as DateTimeValue;
use Ibexa\Core\FieldType\Time\Value as TimeValue;

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

    public function updateDateTime(ContentStruct $struct, string $fieldName, ?DateTimeInterface $value, ?Content $content = null): bool
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
            $newVal = new DateTime('@' . $value->getTimestamp());
            $newVal->setTimezone($value->getTimezone());
            $value = $newVal;
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

        $compareDate = $field->value instanceof DateValue || $field->value instanceof DateTimeValue;
        if ($compareDate && $fieldVal->format('Y-m-d') !== $value->format('Y-m-d')) {
            return false;
        }

        $compareTime = $field->value instanceof TimeValue || $field->value instanceof DateTimeValue;
        if ($compareTime && $fieldVal->format('H:i:s') !== $value->format('H:i:s')) {
            return false;
        }

        return true;
    }

    protected function getDateTimeFieldValue(Field $field): ?DateTime
    {
        switch (true) {
            case $field->value instanceof DateValue:
                return $field->value->date;
            case $field->value instanceof DateTimeValue:
                return $field->value->value;
            case $field->value instanceof TimeValue:
                if ($field->value->time === null) {
                    return null;
                }

                // first create utc time to get actually saved time
                $t = new DateTime();
                $t->setTimestamp($field->value->time);
                $t->setTimezone(new \DateTimeZone('UTC'));

                // return object with correct time and timezone
                return new \DateTime($t->format('H:i:s'));
            default:
                $allowed = [DateValue::class, DateTimeValue::class, TimeValue::class];
                throw InvalidFieldTypeException::fromActualAndExpected($field->value, $allowed);
        }
    }
}
