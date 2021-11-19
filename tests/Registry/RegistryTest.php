<?php declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\Registry;

use Elbformat\FieldHelperBundle\Exception\UnknownFieldHelperException;
use Elbformat\FieldHelperBundle\FieldHelper\BoolFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\TextFieldHelper;
use Elbformat\FieldHelperBundle\Registry\Registry;
use PHPUnit\Framework\TestCase;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class RegistryTest extends TestCase
{
    public function testGetFieldHelper()
    {
        $helpers = [
            TextFieldHelper::class => $this->createMock(TextFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $this->assertInstanceOf(TextFieldHelper::class, $reg->getFieldHelper(TextFieldHelper::class));
    }

    public function testGetTextFieldHelper() {
        $helpers = [
            TextFieldHelper::class => $this->createMock(TextFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $this->assertInstanceOf(TextFieldHelper::class, $reg->getTextFieldHelper());
    }

    public function testGetBoolFieldHelper() {
        $helpers = [
            BoolFieldHelper::class => $this->createMock(BoolFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $this->assertInstanceOf(BoolFieldHelper::class, $reg->getBoolFieldHelper());
    }

    public function testGetFieldHelperUnknown()
    {
        $this->expectException(UnknownFieldHelperException::class);
        $this->expectExceptionMessageMatches('/Unknown FieldHelper: .*TextFieldHelper/');
        $this->expectExceptionMessageMatches('/Valid helpers are:.*BoolFieldHelper/s');
        $helpers = [
            BoolFieldHelper::class => $this->createMock(BoolFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $reg->getFieldHelper(TextFieldHelper::class);
    }
}