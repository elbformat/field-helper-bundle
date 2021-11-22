<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Exception;

/**
 * Thrown, when a wrong helper instance was injected.
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class InvalidFieldHelperException extends \InvalidArgumentException
{
    public static function fromClassName(string $wrongClassName, string $expectedClassName): self
    {
        $msg = sprintf("Invalid FieldHelper. Expected %s but got %s", $expectedClassName, $wrongClassName);

        return new self($msg);
    }
}
