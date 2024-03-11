<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Data;

use Ibexa\Core\FieldType\Url\Value;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class Url
{
    protected function __construct(
        protected ?string $url,
        protected ?string $text
    ) {
    }

    public static function fromUrlValue(Value $value): self
    {
        return new self($value->link, $value->text);
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getText(): ?string
    {
        return $this->text;
    }
}
