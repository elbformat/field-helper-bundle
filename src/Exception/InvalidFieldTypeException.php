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
     * @param object $actual
     * @param string|string[] $expected
     */
    public static function fromActualAndExpected($actual, $expected): self
    {
        if (\is_array($expected)) {
            $expected = implode('|', $expected);
        }
        $msg = sprintf('Expected field type %s but got %s', $expected, \get_class($actual));

        return new self($msg);
    }
}
