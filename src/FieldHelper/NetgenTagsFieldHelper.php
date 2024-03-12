<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Netgen\TagsBundle\API\Repository\TagsService;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\Core\FieldType\Tags\Value as NetgenTagsValue;

class NetgenTagsFieldHelper extends AbstractFieldHelper
{
    protected TagsService $tagsService;

    public function __construct(
        TagsService $tagsService
    ) {
        $this->tagsService = $tagsService;
    }

    public static function getName(): string
    {
        return self::class;
    }

    /**
     * @return Tag[]
     *
     * @throws FieldNotFoundException
     * @throws InvalidFieldTypeException
     */
    public function getTags(Content $content, string $fieldName): array
    {
        $field = $this->getField($content, $fieldName);

        return $this->getNetGenTagFieldValue($field);
    }

    public function getFirstTag(Content $content, string $fieldName): ?Tag
    {
        $result = null;

        $field = $this->getField($content, $fieldName);

        $tags = $this->getNetGenTagFieldValue($field);
        if (count($tags) > 0) {
            $result = reset($tags);
        }

        return $result;
    }

    /**
     * @return Tag[]
     */
    protected function getNetGenTagFieldValue(Field $field): array
    {
        if (!$field->value instanceof NetgenTagsValue) {
            throw InvalidFieldTypeException::fromActualAndExpected($field->value, [NetgenTagsValue::class]);
        }

        return $field->value->tags;
    }
}
