<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\Registry;

use Elbformat\FieldHelperBundle\Exception\UnknownFieldHelperException;
use Elbformat\FieldHelperBundle\FieldHelper\AuthorFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\BoolFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\DateTimeFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\FileFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\ImageFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\NetgenTagsFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\NumberFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\RelationFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\RichtextFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\SelectionFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\TextFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\UrlFieldHelper;
use Elbformat\FieldHelperBundle\Registry\Registry;
use PHPUnit\Framework\TestCase;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class RegistryTest extends TestCase
{
    public function testGetFieldHelper(): void
    {
        $helpers = [
            TextFieldHelper::class => $this->createMock(TextFieldHelper::class),
        ];
        $reg = new Registry($helpers);
        $this->assertInstanceOf(TextFieldHelper::class, $reg->getFieldHelper(TextFieldHelper::class));
    }

    /** @dataProvider getNamedFieldHelperProvider */
    public function testGetNamedFieldHelper(string $method, string $classname): void
    {
        $helpers = [
            $classname => $this->createMock($classname),
        ];
        $reg = new Registry($helpers);
        $this->assertInstanceOf($classname, $reg->$method());
    }

    public function getNamedFieldHelperProvider(): array
    {
        return [
            ['getAuthorFieldHelper',AuthorFieldHelper::class],
            ['getBoolFieldHelper',BoolFieldHelper::class],
            ['getDateTimeFieldHelper',DateTimeFieldHelper::class],
            ['getFileFieldHelper',FileFieldHelper::class],
            ['getImageFieldHelper',ImageFieldHelper::class],
            ['getNetgenTagsFieldHelper',NetgenTagsFieldHelper::class],
            ['getNumberFieldHelper',NumberFieldHelper::class],
            ['getRelationFieldHelper',RelationFieldHelper::class],
            ['getRichtextFieldHelper',RichtextFieldHelper::class],
            ['getSelectionFieldHelper',SelectionFieldHelper::class],
            ['getTextFieldHelper',TextFieldHelper::class],
            ['getUrlFieldHelper',UrlFieldHelper::class],

        ];
    }

    public function testGetFieldHelperUnknown(): void
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
