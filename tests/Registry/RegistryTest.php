<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\Registry;

use Elbformat\FieldHelperBundle\Exception\InvalidFieldHelperException;
use Elbformat\FieldHelperBundle\Exception\UnknownFieldHelperException;
use Elbformat\FieldHelperBundle\FieldHelper\BoolFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\DateTimeFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\NumberFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\RelationFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\MatrixFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\TextFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\UrlFieldHelper;
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

    public function testGetBoolFieldHelper()
    {
        $helpers = [
            BoolFieldHelper::class => $this->createMock(BoolFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $this->assertInstanceOf(BoolFieldHelper::class, $reg->getBoolFieldHelper());
    }

    public function testGetBoolFieldHelperInvalid()
    {
        $this->expectException(InvalidFieldHelperException::class);
        $helpers = [
            BoolFieldHelper::class => $this->createMock(NumberFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $reg->getBoolFieldHelper();
    }

    public function testGetDateTimeFieldHelper()
    {
        $helpers = [
            DateTimeFieldHelper::class => $this->createMock(DateTimeFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $this->assertInstanceOf(DateTimeFieldHelper::class, $reg->getDateTimeFieldHelper());
    }

    public function testGetDateTimeFieldHelperInvalid()
    {
        $this->expectException(InvalidFieldHelperException::class);
        $helpers = [
            DateTimeFieldHelper::class => $this->createMock(NumberFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $reg->getDateTimeFieldHelper();
    }

    public function testGetNumberFieldHelper()
    {
        $helpers = [
            NumberFieldHelper::class => $this->createMock(NumberFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $this->assertInstanceOf(NumberFieldHelper::class, $reg->getNumberFieldHelper());
    }

    public function testGetNumberFieldHelperInvalid()
    {
        $this->expectException(InvalidFieldHelperException::class);
        $helpers = [
            NumberFieldHelper::class => $this->createMock(TextFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $reg->getNumberFieldHelper();
    }

    public function testGetMatrixFieldHelper()
    {
        $helpers = [
            MatrixFieldHelper::class => $this->createMock(MatrixFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $this->assertInstanceOf(MatrixFieldHelper::class, $reg->getMatrixFieldHelper());
    }

    public function testGetTextFieldHelper()
    {
        $helpers = [
            TextFieldHelper::class => $this->createMock(TextFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $this->assertInstanceOf(TextFieldHelper::class, $reg->getTextFieldHelper());
    }

    public function testGetTextFieldHelperInvalid()
    {
        $this->expectException(InvalidFieldHelperException::class);
        $helpers = [
            TextFieldHelper::class => $this->createMock(NumberFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $reg->getTextFieldHelper();
    }

    public function testGetRelationFieldHelper()
    {
        $helpers = [
            RelationFieldHelper::class => $this->createMock(RelationFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $this->assertInstanceOf(RelationFieldHelper::class, $reg->getRelationFieldHelper());
    }

    public function testGetRelationFieldHelperInvalid()
    {
        $this->expectException(InvalidFieldHelperException::class);
        $helpers = [
            RelationFieldHelper::class => $this->createMock(NumberFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $reg->getRelationFieldHelper();
    }

    public function testGetUrlFieldHelper()
    {
        $helpers = [
            UrlFieldHelper::class => $this->createMock(UrlFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $this->assertInstanceOf(UrlFieldHelper::class, $reg->getUrlFieldHelper());
    }

    public function testGetUrlFieldHelperInvalid()
    {
        $this->expectException(InvalidFieldHelperException::class);
        $helpers = [
            UrlFieldHelper::class => $this->createMock(NumberFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $reg->getUrlFieldHelper();
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
