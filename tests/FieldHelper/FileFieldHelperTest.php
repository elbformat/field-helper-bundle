<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Elbformat\FieldHelperBundle\FieldHelper\FileFieldHelper;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\BinaryFile\Value;
use eZ\Publish\Core\FieldType\Float\Value as FloatValue;
use eZ\Publish\Core\Repository\Values\Content\Content;
use PHPUnit\Framework\TestCase;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class FileFieldHelperTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertSame('Elbformat\FieldHelperBundle\FieldHelper\FileFieldHelper', FileFieldHelper::getName());
    }

    public function testGetFileName(): void
    {
        $fh = new FileFieldHelper();
        $content = $this->createContentFromFilename('1px.jpg');
        $this->assertSame('1px.jpg', $fh->getFileName($content, 'file_field'));
    }

    public function testGetFileNameNull(): void
    {
        $fh = new FileFieldHelper();
        $content = $this->createContentFromFilename(null);
        $this->assertNull($fh->getFileName($content, 'file_field'));
    }

    public function testGetFileNameNullValue(): void
    {
        $fh = new FileFieldHelper();
        $field = new Field(['value' => null]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('file_field')->willReturn($field);
        $this->assertNull($fh->getFileName($content, 'file_field'));
    }

    public function testGetFileNameFieldNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $fh = new FileFieldHelper();
        $content = $this->createMock(Content::class);
        $fh->getFileName($content, 'not_a_bool_field');
    }

    public function testGetFileNameInvalidFieldType(): void
    {
        $this->expectException(InvalidFieldTypeException::class);
        $this->expectExceptionMessageMatches('/Expected field type .*Checkbox\\\\Value/');
        $this->expectExceptionMessageMatches('/but got .*Float\\\\Value/');
        $fh = new FileFieldHelper();
        $field = new Field(['value' => new FloatValue(1.0)]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('file_field')->willReturn($field);
        $fh->getFileName($content, 'file_field');
    }

    protected function createContentFromFilename(?string $filename): Content
    {
        $field = new Field(['value' => new Value(['fileName' => $filename])]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('file_field')->willReturn($field);

        return $content;
    }
}
