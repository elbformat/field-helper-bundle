<?php

namespace Elbformat\FieldHelperBundle\Registry;

use Elbformat\FieldHelperBundle\FieldHelper\BoolFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\DateTimeFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\FieldHelperInterface;
use Elbformat\FieldHelperBundle\FieldHelper\NumberFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\RelationFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\MatrixFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\TextFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\UrlFieldHelper;

interface RegistryInterface
{
    public function getFieldHelper(string $class): FieldHelperInterface;

    public function getBoolFieldHelper(): BoolFieldHelper;

    public function getDateTimeFieldHelper(): DateTimeFieldHelper;

    public function getMatrixFieldHelper(): MatrixFieldHelper;

    public function getNumberFieldHelper(): NumberFieldHelper;

    public function getRelationFieldHelper(): RelationFieldHelper;

    public function getTextFieldHelper(): TextFieldHelper;

    public function getUrlFieldHelper(): UrlFieldHelper;
}
