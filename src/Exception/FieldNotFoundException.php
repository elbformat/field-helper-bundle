<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Exception;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;

/**
 * Thrown, when a field by the given name was not found in this content object.
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class FieldNotFoundException extends \DomainException
{
    public static function fromContentAndField(Content $content, string $fieldName): self
    {
        $msg = sprintf('Field %s not found in content-type %s', $fieldName, $content->getContentType()->identifier);

        return new self($msg);
    }
}
