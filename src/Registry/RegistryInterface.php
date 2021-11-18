<?php

namespace Elbformat\FieldHelperBundle\Registry;

use Elbformat\FieldHelperBundle\FieldHelper\FieldHelperInterface;

interface RegistryInterface
{
    public function getFieldHelper(string $class): FieldHelperInterface;
}