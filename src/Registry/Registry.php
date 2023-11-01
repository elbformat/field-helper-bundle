<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Registry;

use Elbformat\FieldHelperBundle\Exception\InvalidFieldHelperException;
use Elbformat\FieldHelperBundle\Exception\UnknownFieldHelperException;
use Elbformat\FieldHelperBundle\FieldHelper\AuthorFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\BoolFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\DateTimeFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\FieldHelperInterface;
use Elbformat\FieldHelperBundle\FieldHelper\FileFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\ImageFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\MatrixFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\NetgenTagsFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\NumberFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\RelationFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\RichtextFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\SelectionFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\TextFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\UrlFieldHelper;

/**
 * Single Service to inject for easier access to multiple field helpers.
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class Registry implements RegistryInterface
{
    /**
     * @template T of FieldHelperInterface
     * @param array<class-string<T>,T> $helper
     */
    public function __construct(protected array $helper)
    {
    }

    /**
     * @template T of FieldHelperInterface
     * @param class-string<T> $class
     * @return T
     */
    public function getFieldHelper(string $class): FieldHelperInterface
    {
        if (!isset($this->helper[$class])) {
            throw UnknownFieldHelperException::fromClassName($class, array_keys($this->helper));
        }

        return $this->helper[$class];
    }

    // The following are type-hinted shortcuts to built-in field helpers. That makes the usage much easier.

    public function getAuthorFieldHelper(): AuthorFieldHelper
    {
        return $this->getFieldHelper(AuthorFieldHelper::class);
    }

    public function getBoolFieldHelper(): BoolFieldHelper
    {
        return $this->getFieldHelper(BoolFieldHelper::class);
    }

    public function getDateTimeFieldHelper(): DateTimeFieldHelper
    {
        return $this->getFieldHelper(DateTimeFieldHelper::class);

    }

    public function getFileFieldHelper(): FileFieldHelper
    {
        return $this->getFieldHelper(FileFieldHelper::class);
    }

    public function getImageFieldHelper(): ImageFieldHelper
    {
        return $this->getFieldHelper(ImageFieldHelper::class);
    }

    public function getMatrixFieldHelper(): MatrixFieldHelper
    {
        return $this->getFieldHelper(MatrixFieldHelper::class);
    }

    public function getNetgenTagsFieldHelper(): NetgenTagsFieldHelper
    {
        return $this->getFieldHelper(NetgenTagsFieldHelper::class);
    }

    public function getNumberFieldHelper(): NumberFieldHelper
    {
        return $this->getFieldHelper(NumberFieldHelper::class);

    }

    public function getRelationFieldHelper(): RelationFieldHelper
    {
        return $this->getFieldHelper(RelationFieldHelper::class);

    }

    public function getRichtextFieldHelper(): RichtextFieldHelper
    {
        return $this->getFieldHelper(RichtextFieldHelper::class);

    }

    public function getSelectionFieldHelper(): SelectionFieldHelper
    {
        return $this->getFieldHelper(SelectionFieldHelper::class);

    }

    public function getTextFieldHelper(): TextFieldHelper
    {
        return $this->getFieldHelper(TextFieldHelper::class);

    }

    public function getUrlFieldHelper(): UrlFieldHelper
    {
        return $this->getFieldHelper(UrlFieldHelper::class);

    }


}
