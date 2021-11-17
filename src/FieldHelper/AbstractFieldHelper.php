<?php
declare(strict_types=1);

namespace IbexaFieldHelperBundle\FieldHelper;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use IbexaFieldHelperBundle\Exception\FieldNotFoundException;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
abstract class AbstractFieldHelper extends FieldHelperInterface
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
