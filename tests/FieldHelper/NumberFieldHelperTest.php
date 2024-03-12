<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Elbformat\FieldHelperBundle\FieldHelper\NumberFieldHelper;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\Float\Value as FloatValue;
use Ibexa\Core\FieldType\Integer\Value as IntValue;
use Ibexa\Core\FieldType\TextLine\Value as TextLineValue;
use Ibexa\Core\FieldType\Value;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Core\Repository\Values\Content\ContentUpdateStruct;
use PHPUnit\Framework\TestCase;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class NumberFieldHelperTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertSame('Elbformat\FieldHelperBundle\FieldHelper\NumberFieldHelper', NumberFieldHelper::getName());
    }

    /**
     * @dataProvider getIntegerProvider
     */
    public function testGetInteger(Value $value, ?int $expectedString): void
    {
        $fh = new NumberFieldHelper();
        $content = $this->createContentFromValue($value);
        $this->assertSame($expectedString, $fh->getInteger($content, 'number_field'));
    }

    public function getIntegerProvider(): array
    {
        return [
            [new IntValue(0), 0],
            [new IntValue(3), 3],
            [new FloatValue(1.2), 1],
            [new FloatValue(1.5), 2],
        ];
    }

    /**
     * @dataProvider getFloatProvider
     */
    public function testGetFloat(Value $value, ?float $expectedString): void
    {
        $fh = new NumberFieldHelper();
        $content = $this->createContentFromValue($value);
        $this->assertSame($expectedString, $fh->getFloat($content, 'number_field'));
    }

    public function getFloatProvider(): array
    {
        return [
            [new IntValue(0), 0.0],
            [new IntValue(3), 3.0],
            [new FloatValue(1.0), 1.0],
            [new FloatValue(1.2), 1.2],
        ];
    }

    public function testGetIntegerFieldNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $fh = new NumberFieldHelper();
        $content = $this->createMock(Content::class);
        $fh->getInteger($content, 'not_a_number_field');
    }

    public function testGetFloatFieldNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $fh = new NumberFieldHelper();
        $content = $this->createMock(Content::class);
        $fh->getFloat($content, 'not_a_number_field');
    }

    public function testGetIntegerInvalidFieldType(): void
    {
        $this->expectException(InvalidFieldTypeException::class);
        $this->expectExceptionMessageMatches('/Expected field type .*Integer\\\\Value/');
        $this->expectExceptionMessageMatches('/Expected field type .*Float\\\\Value/');
        $this->expectExceptionMessageMatches('/but got .*TextLine\\\\Value/');
        $fh = new NumberFieldHelper();
        $content = $this->createContentFromValue(new TextLineValue());
        $fh->getInteger($content, 'number_field');
    }

    public function testGetFloatInvalidFieldType(): void
    {
        $this->expectException(InvalidFieldTypeException::class);
        $this->expectExceptionMessageMatches('/Expected field type .*Float\\\\Value/');
        $this->expectExceptionMessageMatches('/Expected field type .*Integer\\\\Value/');
        $this->expectExceptionMessageMatches('/but got .*TextLine\\\\Value/');
        $fh = new NumberFieldHelper();
        $content = $this->createContentFromValue(new TextLineValue());
        $fh->getFloat($content, 'number_field');
    }

    public function testUpdateIntegerCreate(): void
    {
        $fh = new NumberFieldHelper();
        $struct = new ContentCreateStruct();
        $this->assertTrue($fh->updateInteger($struct, 'number_field', 3));
        $this->assertSame('number_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertSame(3, $struct->fields[0]->value);
    }

    public function testUpdateFloatCreate(): void
    {
        $fh = new NumberFieldHelper();
        $struct = new ContentCreateStruct();
        $this->assertTrue($fh->updateFloat($struct, 'number_field', 1.2));
        $this->assertSame('number_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertSame(1.2, $struct->fields[0]->value);
    }

    public function testUpdateStringChanged(): void
    {
        $fh = new NumberFieldHelper();
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromValue(new IntValue(2));
        $this->assertTrue($fh->updateInteger($struct, 'number_field', 3, $content));
        $this->assertSame('number_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertSame(3, $struct->fields[0]->value);
    }

    public function testUpdateFloatChanged(): void
    {
        $fh = new NumberFieldHelper();
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromValue(new FloatValue(2.2));
        $this->assertTrue($fh->updateFloat($struct, 'number_field', 1.2, $content));
        $this->assertSame('number_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertSame(1.2, $struct->fields[0]->value);
    }

    public function testUpdateStringUnchanged(): void
    {
        $fh = new NumberFieldHelper();
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromValue(new IntValue(3));
        $this->assertFalse($fh->updateInteger($struct, 'number_field', 3, $content));
        $this->assertCount(0, $struct->fields);
    }

    public function testUpdateFloatUnchanged(): void
    {
        $fh = new NumberFieldHelper();
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromValue(new FloatValue(3.3));
        $this->assertFalse($fh->updateFloat($struct, 'number_field', 3.3, $content));
        $this->assertCount(0, $struct->fields);
    }

    protected function createContentFromValue(Value $value): Content
    {
        $field = new Field(['value' => $value]);

        $content = $this->createMock(Content::class);
        $content->method('getField')->with('number_field')->willReturn($field);

        return $content;
    }
}
