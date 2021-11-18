<?php declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\Registry;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Elbformat\FieldHelperBundle\Exception\NotSetException;
use Elbformat\FieldHelperBundle\Exception\UnknownHelperException;
use Elbformat\FieldHelperBundle\FieldHelper\BoolFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\TextFieldHelper;
use Elbformat\FieldHelperBundle\Registry\Registry;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\EmailAddress\Value as EmailAddressValue;
use eZ\Publish\Core\FieldType\Float\Value as FloatValue;
use eZ\Publish\Core\FieldType\TextBlock\Value as TextBlockValue;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\Core\Repository\Values\Content\Content;
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
        $this->expectException(UnknownHelperException::class);
        $this->expectExceptionMessageMatches('/Unknown FieldHelper: .*TextFieldHelper/');
        $this->expectExceptionMessageMatches('/Valid helpers are:.*BoolFieldHelper/s');
        $helpers = [
            BoolFieldHelper::class => $this->createMock(BoolFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $reg->getFieldHelper(TextFieldHelper::class);
    }
}