<?php

namespace Elbformat\FieldHelperBundle\Registry;

use Elbformat\FieldHelperBundle\FieldHelper\BoolFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\DateTimeFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\FieldHelperInterface;
use Elbformat\FieldHelperBundle\FieldHelper\TextFieldHelper;

interface RegistryInterface
{
    public function getFieldHelper(string $class): FieldHelperInterface;

    public function getBoolFieldHelper(): BoolFieldHelper;

    public function getDateTimeFieldHelper(): DateTimeFieldHelper;

    public function getTextFieldHelper(): TextFieldHelper;
}