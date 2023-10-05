<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentStruct;
use eZ\Publish\Core\FieldType\Relation\Value as RelationValue;
use eZ\Publish\Core\FieldType\RelationList\Value as RelationListValue;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use Psr\Log\LoggerInterface;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class RelationFieldHelper extends AbstractFieldHelper
{
    public function __construct(
        protected Repository $repo,
        protected ResponseTagger $responseTagger,
        protected LoggerInterface $logger
    ) {
    }

    public static function getName(): string
    {
        return self::class;
    }

    /**
     * @throws FieldNotFoundException
     * @throws InvalidFieldTypeException
     */
    public function getOneContent(Content $content, string $fieldName): ?Content
    {
        $ids = $this->getRelationIds($content, $fieldName);
        if (!count($ids)) {
            return null;
        }
        $id = $ids[0];

        $targetContent = $this->repo->sudo(
            function (Repository $repo) use ($id) {
                return $repo->getContentService()->loadContent($id);
            }
        );
        $this->responseTagger->tag($targetContent->contentInfo);

        return $targetContent;
    }

    /**
     * @return Content[]
     * @throws FieldNotFoundException
     * @throws InvalidFieldTypeException
     */
    public function getContents(Content $content, string $fieldName): array
    {
        $ids = $this->getRelationIds($content, $fieldName);

        $contents = [];
        foreach ($ids as $contentId) {
            try {
                $childContent = $this->repo->sudo(
                    function (Repository $repo) use ($contentId) {
                        return $repo->getContentService()->loadContent($contentId);
                    }
                );
                $this->responseTagger->tag($childContent->contentInfo);
                $contents[] = $childContent;
            } catch (\Throwable $t) {
                $logCtx = [
                    'exception' => $t->getMessage(),
                    'exceptionType' => \get_class($t),
                    'contentId' => $contentId,
                ];
                $this->logger->error('Error loading related content', $logCtx);
            }
        }

        return $contents;
    }

    /**
     * @param int[] $contentIds
     * @throws FieldNotFoundException
     * @throws InvalidFieldTypeException
     */
    public function updateRelation(ContentStruct $struct, string $fieldName, array $contentIds, ?Content $content = null): bool
    {
        // No changes
        if (null !== $content) {
            $existingIds = $this->getRelationIds($content, $fieldName);
            if (!array_diff($existingIds, $contentIds) && !array_diff($contentIds, $existingIds)) {
                return false;
            }
        }
        $struct->setField($fieldName, $contentIds);

        return true;
    }

    /** @return int[] */
    protected function getRelationIds(Content $content, string $fieldName): array
    {
        $field = $this->getField($content, $fieldName);
        if ($field->value instanceof RelationListValue) {
            $stringIds = $field->value->destinationContentIds;
            // Make integers from mixed
            $ids = [];
            /** @var mixed $stringId */
            foreach ($stringIds as $stringId) {
                $ids[] = (int) $stringId;
            }
        } elseif($field->value instanceof RelationValue) {
            $ids = $field->value->destinationContentId ? [(int)$field->value->destinationContentId] : [];
        } else {
            /** @psalm-suppress DeprecatedClass */
            $allowed = [RelationListValue::class, RelationValue::class];
            throw InvalidFieldTypeException::fromActualAndExpected($field->value, $allowed);
        }

        return $ids;
    }
}
