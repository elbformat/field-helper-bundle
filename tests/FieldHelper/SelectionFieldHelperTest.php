<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Elbformat\FieldHelperBundle\FieldHelper\FileFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\SelectionFieldHelper;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\FieldType\Float\Value as FloatValue;
use Ibexa\Core\FieldType\Selection\Value;
use Ibexa\Core\Repository\Values\Content\Content;
use PHPUnit\Framework\TestCase;

class SelectionFieldHelperTest extends TestCase
{
    public function testGetValue(): void
    {
        $content = $this->createContentFromValue(['lorem', 'ipsum'], [1]);

        $fh = new SelectionFieldHelper();
        $this->assertSame(1, $fh->getValue($content, 'select_field'));
    }

    public function testGetValueFirst(): void
    {
        $content = $this->createContentFromValue(['lorem', 'ipsum'], [0,1]);

        $fh = new SelectionFieldHelper();
        $this->assertSame(0, $fh->getValue($content, 'select_field'));
    }

    public function testGetValueNull(): void
    {
        $content = $this->createContentFromValue(['lorem', 'ipsum'], []);

        $fh = new SelectionFieldHelper();
        $this->assertNull($fh->getValue($content, 'select_field'));
    }

    public function testGetMultipleValues(): void
    {
        $content = $this->createContentFromValue(['lorem', 'ipsum'], [0,1]);

        $fh = new SelectionFieldHelper();
        $this->assertSame([0,1], $fh->getMultipleValues($content, 'select_field'));
    }

    public function testGetMultipleValuesEmpty(): void
    {
        $content = $this->createContentFromValue(['lorem', 'ipsum'], []);

        $fh = new SelectionFieldHelper();
        $this->assertSame([], $fh->getMultipleValues($content, 'select_field'));
    }

    public function testSelectionName(): void
    {
        $content = $this->createContentFromValue(['lorem', 'ipsum'], [1]);

        $fh = new SelectionFieldHelper();
        $this->assertSame('ipsum', $fh->getSelectionName($content, 'select_field'));
    }

    public function testSelectionNameEmpty(): void
    {
        $content = $this->createContentFromValue(['lorem', 'ipsum'], []);

        $fh = new SelectionFieldHelper();
        $this->assertNull($fh->getSelectionName($content, 'select_field'));
    }

    public function testInvalidFieldType(): void
    {
        $this->expectException(InvalidFieldTypeException::class);
        $this->expectExceptionMessageMatches('/Expected field type .*Selection\\\\Value/');
        $this->expectExceptionMessageMatches('/but got .*Float\\\\Value/');
        $fh = new SelectionFieldHelper();
        $field = new Field(['value' => new FloatValue(1.0)]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('select_field')->willReturn($field);
        $fh->getSelectionName($content, 'select_field');
    }

    protected function createContentFromValue(array $options, ?array $selected): Content
    {
        $field = new Field(['value' => new Value($selected)]);

        $content = $this->createMock(Content::class);
        $content->method('getField')->with('select_field')->willReturn($field);
        $contentType = $this->createMock(ContentType::class);
        $fieldDef = $this->createMock(FieldDefinition::class);
        $fieldSettings = ['options' => $options];
        $fieldDef->method('getFieldSettings')->willReturn($fieldSettings);
        $contentType->method('getFieldDefinition')->willReturn($fieldDef);
        $content->method('getContentType')->willReturn($contentType);

        return $content;
    }
}
