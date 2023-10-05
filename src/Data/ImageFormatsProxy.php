<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Data;

use ArrayAccess;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\FieldType\Image\Value;
use eZ\Publish\SPI\Variation\VariationHandler;

/**
 * This proxy makes sure, that image formats will only be generated as soon as they are needed in the template.
 *
 * @implements ArrayAccess<string,string>
 */
class ImageFormatsProxy implements \ArrayAccess
{
    /** @param string[] $variationNames */
    public function __construct(
        protected VariationHandler $variationHandler,
        protected Field $field,
        protected VersionInfo $versionInfo,
        protected array $variationNames
    ) {
    }

    public function offsetExists(mixed $offset): bool
    {
        return \in_array($offset, $this->variationNames, true);
    }

    public function offsetGet(mixed $offset): string
    {
        if ('original' === $offset) {
            if (!$this->field->value instanceof Value || null === $this->field->value->uri) {
                throw new \RuntimeException('No original image found');
            }

            return $this->field->value->uri;
        }

        $variation = $this->variationHandler->getVariation($this->field, $this->versionInfo, $offset);

        return (string) parse_url($variation->uri, \PHP_URL_PATH);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \InvalidArgumentException('readonly');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \InvalidArgumentException('readonly');
    }
}
