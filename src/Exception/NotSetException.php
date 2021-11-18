<?php
declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Exception;

use eZ\Publish\API\Repository\Values\Content\Content;

/**
 * Thrown, when a required field is not set.
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class NotSetException extends \RuntimeException
{
    public static function fromContentAndFieldName(Content $content, string $fieldName): self
    {
        $msg = sprintf('Field %s is empty on content %d.', $fieldName, $content->id);

        return new self($msg);
    }
}
