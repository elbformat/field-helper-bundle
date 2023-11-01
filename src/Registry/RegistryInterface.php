<?php

namespace Elbformat\FieldHelperBundle\Registry;

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

interface RegistryInterface
{
    /**
     * @template T of FieldHelperInterface
     * @param class-string<T> $class
     * @return T
     */
    public function getFieldHelper(string $class): FieldHelperInterface;

    public function getAuthorFieldHelper(): AuthorFieldHelper;
    public function getBoolFieldHelper(): BoolFieldHelper;
    public function getDateTimeFieldHelper(): DateTimeFieldHelper;
    public function getFileFieldHelper(): FileFieldHelper;
    public function getImageFieldHelper(): ImageFieldHelper;
    public function getMatrixFieldHelper(): MatrixFieldHelper;
    public function getNetgenTagsFieldHelper(): NetgenTagsFieldHelper;
    public function getNumberFieldHelper(): NumberFieldHelper;
    public function getRelationFieldHelper(): RelationFieldHelper;
    public function getRichtextFieldHelper(): RichtextFieldHelper;
    public function getSelectionFieldHelper(): SelectionFieldHelper;
    public function getTextFieldHelper(): TextFieldHelper;
    public function getUrlFieldHelper(): UrlFieldHelper;

}
