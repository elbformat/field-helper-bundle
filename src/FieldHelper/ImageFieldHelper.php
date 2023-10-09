<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\FieldHelper;

use Elbformat\FieldHelperBundle\Data\Image;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\RelationList\Value as RelationValue;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Ibexa\Contracts\Core\Variation\VariationHandler;
use Ibexa\Contracts\HttpCache\ResponseTagger\ResponseTagger;
use eZ\Publish\Core\FieldType\Image\Value as ImageValue;
use eZ\Publish\Core\FieldType\ImageAsset\Value as ImageAssetValue;

/**
 * Create an image object from Image content object, or a filed with a relation
 * to an image object (via object relation or ImageAsset).
 */
class ImageFieldHelper extends AbstractFieldHelper
{
    public function __construct(
        protected VariationHandler $variationHandler,
        protected Repository $repo,
        protected ConfigResolverInterface $config,
        protected ResponseTagger $responseTagger,
        protected RichtextFieldHelper $fhRichtext,
        protected RelationFieldHelper $fhRel,
    ) {
    }

    public function getImage(Content $content, string $fieldName = 'image'): ?Image
    {
        $field = $this->getImageValueField($content, $fieldName);

        if (null === $field) {
            return null;
        }
        $imageValue = $this->getImageFieldValue($field);

        /** @var array<string,mixed> $variationConfig */
        $variationConfig = $this->config->getParameter('image_variations');
        $variations = array_keys($variationConfig);
        $variations[] = 'original';
        $caption = $this->getCaption($content, $fieldName);

        return new Image($imageValue->alternativeText, $caption, $this->variationHandler, $field, $content->getVersionInfo(), $variations);
    }

    protected function getImageValueField(Content $content, string $fieldName): ?Field
    {
        $field = $this->getField($content, $fieldName);

        // Get image from relation
        if ($field->value instanceof RelationValue) {
            try {
                $relationContent = $this->fhRel->getOneContent($content, $fieldName);
                if (null === $relationContent) {
                    return null;
                }

                return $this->getImageValueField($relationContent, 'image');
            } catch (NotFoundException $e) {
                // No image set
                return null;
            }
        }

        // Try to get ImageValue from ImageAsset
        if ($field->value instanceof ImageAssetValue) {
            // No image stored
            if (!is_numeric($field->value->destinationContentId)) {
                return null;
            }

            $imageObj = $this->repo->getContentService()->loadContent((int) $field->value->destinationContentId);
            $this->responseTagger->tag($imageObj->contentInfo);
            $field = $this->getField($imageObj, 'image');
        }

        return $field;
    }

    protected function getCaption(Content $content, string $fieldName): ?string
    {
        if ('image' === $content->getContentType()->identifier) {
            return $this->fhRichtext->getHtml($content, 'caption');
        }

        $field = $this->getField($content, $fieldName);

        // Try to get from relation
        if ($field->value instanceof RelationValue) {
            try {
                $relationContent = $this->fhRel->getOneContent($content, $fieldName);
                if (null === $relationContent) {
                    return null;
                }

                return $this->fhRichtext->getHtml($relationContent, 'caption');
            } catch (NotFoundException $e) {
                // No image set
                return null;
            }
        }

        // Try to get ImageValue from ImageAsset
        if ($field->value instanceof ImageAssetValue) {
            // No image stored
            if (!is_numeric($field->value->destinationContentId)) {
                return null;
            }

            $imageObj = $this->repo->getContentService()->loadContent((int) $field->value->destinationContentId);

            return $this->fhRichtext->getHtml($imageObj, 'caption');
        }

        return '';
    }

    protected function getImageFieldValue(Field $field): ImageValue
    {
        if (null === $field->value) {
            return new ImageValue();
        }
        if (!$field->value instanceof ImageValue) {
            throw InvalidFieldTypeException::fromActualAndExpected($field->value, [ImageValue::class]);
        }

        return $field->value;
    }
}
