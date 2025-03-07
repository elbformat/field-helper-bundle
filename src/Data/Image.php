<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Data;

use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Core\Variation\VariationHandler;

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
