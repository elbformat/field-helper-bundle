<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
abstract class AbstractFieldHelper implements FieldHelperInterface
{
    /**
     * @throws FieldNotFoundException
     */
    protected function getField(Content $content, string $fieldName): Field
    {
        $field = $content->getField($fieldName);
        if (null === $field) {
            throw FieldNotFoundException::fromContentAndField($content, $fieldName);
        }

        return $field;
    }
}
