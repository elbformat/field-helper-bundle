<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Elbformat\FieldHelperBundle\FieldHelper\BoolFieldHelper;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\Checkbox\Value as CheckboxValue;
use Ibexa\Core\FieldType\Float\Value as FloatValue;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Core\Repository\Values\Content\ContentUpdateStruct;
use PHPUnit\Framework\TestCase;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class BoolFieldHelperTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertSame('Elbformat\FieldHelperBundle\FieldHelper\BoolFieldHelper', BoolFieldHelper::getName());
    }

    public function testGetBoolTrue(): void
    {
        $fh = new BoolFieldHelper();
        $content = $this->createContentFromBool(true);
        $this->assertTrue($fh->getBool($content, 'bool_field'));
    }

    public function testGetBoolFalse(): void
    {
        $fh = new BoolFieldHelper();
        $content = $this->createContentFromBool(false);
        $this->assertFalse($fh->getBool($content, 'bool_field'));
    }

    public function testGetBoolNull(): void
    {
        $fh = new BoolFieldHelper();
        $content = $this->createContentFromBool(null);
        $this->assertNull($fh->getBool($content, 'bool_field'));
    }

    public function testGetBoolFieldNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $fh = new BoolFieldHelper();
        $content = $this->createMock(Content::class);
        $fh->getBool($content, 'not_a_bool_field');
    }

    public function testGetBoolInvalidFieldType(): void
    {
        $this->expectException(InvalidFieldTypeException::class);
        $this->expectExceptionMessageMatches('/Expected field type .*Checkbox\\\\Value/');
        $this->expectExceptionMessageMatches('/but got .*Float\\\\Value/');
        $fh = new BoolFieldHelper();
        $field = new Field(['value' => new FloatValue(1.0)]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('bool_field')->willReturn($field);
        $fh->getBool($content, 'bool_field');
    }

    public function testUpdateBoolCreate(): void
    {
        $fh = new BoolFieldHelper();
        $struct = new ContentCreateStruct();
        $this->assertTrue($fh->updateBool($struct, 'bool_field', true));
        $this->assertEquals('bool_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertTrue($struct->fields[0]->value);
    }

    public function testUpdateBoolChanged(): void
    {
        $fh = new BoolFieldHelper();
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromBool(true);
        $this->assertTrue($fh->updateBool($struct, 'bool_field', false, $content));
        $this->assertEquals('bool_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertFalse($struct->fields[0]->value);
    }

    public function testUpdateBoolUnchanged(): void
    {
        $fh = new BoolFieldHelper();
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromBool(true);
        $this->assertFalse($fh->updateBool($struct, 'bool_field', true, $content));
        $this->assertCount(0, $struct->fields);
    }

    protected function createContentFromBool(?bool $value): Content
    {
        $field = new Field(['value' => new CheckboxValue($value)]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('bool_field')->willReturn($field);

        return $content;
    }
}
