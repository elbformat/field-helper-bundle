<?php

namespace Elbformat\IbexaFieldHelperBundle\Registry;

use Elbformat\IbexaFieldHelperBundle\FieldHelper\FieldHelperInterface;

interface RegistryInterface
{
    public function getFieldHelper(string $class): FieldHelperInterface;
}