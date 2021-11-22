<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Exception;

/**
 * Thrown, when a field is not of the expected type.
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class InvalidFieldTypeException extends \InvalidArgumentException
{
    /**
     * @param mixed $actualClass
     * @param string[] $expectedClasses
     */
    public static function fromActualAndExpected($actualClass, array $expectedClasses): self
    {
        $expectedString = implode('|', $expectedClasses);
        $msg = sprintf('Expected field type %s but got %s', $expectedString, is_object($actualClass) ? \get_class($actualClass) : (string)$actualClass);

        return new self($msg);
    }
}
