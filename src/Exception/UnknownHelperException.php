<?php
declare(strict_types=1);

namespace Elbformat\IbexaFieldHelperBundle\Exception;

/**
 * Thrown, when a helper was requested that does not exist.
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class UnknownHelperException extends \InvalidArgumentException
{
    /** @param array<int,string> $validClasses */
    public static function fromClassName(string $className, array $validClasses): self
    {
        $msg = sprintf("Unknown FieldHelper: %s. Valid helpers are:\n %s", $className, implode("\n",$validClasses));

        return new self($msg);
    }
}
