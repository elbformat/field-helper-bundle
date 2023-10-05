<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Data;

use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\SPI\Variation\VariationHandler;

class Image
{
    protected ?string $alt;
    protected ?string $caption;
    protected ImageFormatsProxy $formats;

    /** @param string[] $variations */
    public function __construct(?string $alt, ?string $caption, VariationHandler $variationHandler, Field $field, VersionInfo $versionInfo, array $variations)
    {
        $this->alt = $alt;
        $this->caption = $caption;
        $this->formats = new ImageFormatsProxy($variationHandler, $field, $versionInfo, $variations);
    }

    public function getFormats(): ImageFormatsProxy
    {
        return $this->formats;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }
}
