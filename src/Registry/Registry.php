<?php
declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Registry;

use Elbformat\FieldHelperBundle\Exception\UnknownHelperException;
use Elbformat\FieldHelperBundle\FieldHelper\BoolFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\FieldHelperInterface;
use Elbformat\FieldHelperBundle\FieldHelper\TextFieldHelper;

/**
 * Single Service to inject for easier access to multiple field helpers.
 *
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class Registry implements RegistryInterface
{

    /** @var array <string,FieldHelperInterface> */
    protected $helper = [];

    public function __construct(array $helper)
    {
        $this->helper = $helper;
    }

    public function getFieldHelper(string $class): FieldHelperInterface
    {
        if (!isset($this->helper[$class])) {
            throw UnknownHelperException::fromClassName($class,array_keys($this->helper));
        }
        return $this->helper[$class];
    }

    // The following are type-hinted shortcuts to built-in field helpers. That makes the usage much easier.

    public function getBoolFieldHelper(): BoolFieldHelper
    {
        return $this->getFieldHelper(BoolFieldHelper::class);
    }

    public function getTextFieldHelper(): TextFieldHelper
    {
        return $this->getFieldHelper(TextFieldHelper::class);
    }

}
