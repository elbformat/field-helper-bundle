<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Registry;

use Elbformat\FieldHelperBundle\Exception\InvalidFieldHelperException;
use Elbformat\FieldHelperBundle\Exception\UnknownFieldHelperException;
use Elbformat\FieldHelperBundle\FieldHelper\BoolFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\DateTimeFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\FieldHelperInterface;
use Elbformat\FieldHelperBundle\FieldHelper\NumberFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\RelationFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\RichtextFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\TextFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\UrlFieldHelper;

/**
 * Single Service to inject for easier access to multiple field helpers.
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class Registry implements RegistryInterface
{
    /** @var array<string,FieldHelperInterface> */
    protected $helper = [];

    /** @param array<string,FieldHelperInterface> $helper */
    public function __construct(array $helper)
    {
        $this->helper = $helper;
    }

    public function getFieldHelper(string $class): FieldHelperInterface
    {
        if (!isset($this->helper[$class])) {
            throw UnknownFieldHelperException::fromClassName($class, array_keys($this->helper));
        }

        return $this->helper[$class];
    }

    // The following are type-hinted shortcuts to built-in field helpers. That makes the usage much easier.

    public function getBoolFieldHelper(): BoolFieldHelper
    {
        $fh = $this->getFieldHelper(BoolFieldHelper::class);
        if (!$fh instanceof BoolFieldHelper) {
            throw InvalidFieldHelperException::fromClassName(get_class($fh), BoolFieldHelper::class);
        }

        return $fh;
    }

    public function getDateTimeFieldHelper(): DateTimeFieldHelper
    {
        $fh = $this->getFieldHelper(DateTimeFieldHelper::class);
        if (!$fh instanceof DateTimeFieldHelper) {
            throw InvalidFieldHelperException::fromClassName(get_class($fh), DateTimeFieldHelper::class);
        }

        return $fh;
    }

    public function getNumberFieldHelper(): NumberFieldHelper
    {
        $fh = $this->getFieldHelper(NumberFieldHelper::class);
        if (!$fh instanceof NumberFieldHelper) {
            throw InvalidFieldHelperException::fromClassName(get_class($fh), NumberFieldHelper::class);
        }

        return $fh;
    }

    public function getRelationFieldHelper(): RelationFieldHelper
    {
        $fh = $this->getFieldHelper(RelationFieldHelper::class);
        if (!$fh instanceof RelationFieldHelper) {
            throw InvalidFieldHelperException::fromClassName(get_class($fh), RelationFieldHelper::class);
        }

        return $fh;
    }

    public function getTextFieldHelper(): TextFieldHelper
    {
        $fh = $this->getFieldHelper(TextFieldHelper::class);
        if (!$fh instanceof TextFieldHelper) {
            throw InvalidFieldHelperException::fromClassName(get_class($fh), TextFieldHelper::class);
        }

        return $fh;
    }

    public function getUrlFieldHelper(): UrlFieldHelper
    {
        $fh = $this->getFieldHelper(UrlFieldHelper::class);
        if (!$fh instanceof UrlFieldHelper) {
            throw InvalidFieldHelperException::fromClassName(get_class($fh), UrlFieldHelper::class);
        }

        return $fh;
    }

    public function getRichtextFieldHelper(): RichtextFieldHelper
    {
        $fh = $this->getFieldHelper(RichtextFieldHelper::class);
        if (!$fh instanceof RichtextFieldHelper) {
            throw InvalidFieldHelperException::fromClassName(get_class($fh), RichtextFieldHelper::class);
        }

        return $fh;
    }
}
