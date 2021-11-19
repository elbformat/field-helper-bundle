<?php declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Elbformat\FieldHelperBundle\Exception\NotSetException;
use Elbformat\FieldHelperBundle\FieldHelper\TextFieldHelper;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\EmailAddress\Value as EmailAddressValue;
use eZ\Publish\Core\FieldType\Float\Value as FloatValue;
use eZ\Publish\Core\FieldType\TextBlock\Value as TextBlockValue;
use eZ\Publish\Core\FieldType\TextLine\Value as TextLineValue;
use eZ\Publish\Core\FieldType\Value;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use PHPUnit\Framework\TestCase;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class TextFieldHelperTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertSame('Elbformat\FieldHelperBundle\FieldHelper\TextFieldHelper', TextFieldHelper::getName());
    }

    /**
     * @dataProvider getStringProvider
     */
    public function testGetString(Value $value, ?string $expectedString): void
    {
        $fh = new TextFieldHelper();
        $content = $this->createContentFromValue($value);
        $this->assertSame($expectedString, $fh->getString($content, 'text_field'));
    }

    public function getStringProvider(): array
    {
        return [
            [new TextLineValue('My Text'), 'My Text'],
            [new TextBlockValue('My Text'), 'My Text'],
            [new EmailAddressValue('test@email.de'), 'test@email.de'],
            [new TextLineValue(''), ''],
            [new TextLineValue(null), null],
        ];
    }

    public function testGetStringFieldNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $fh = new TextFieldHelper();
        $content = $this->createMock(Content::class);
        $fh->getString($content, 'not_a_text_field');
    }

    public function testGetStringInvalidFieldType(): void
    {
        $this->expectException(InvalidFieldTypeException::class);
        $this->expectExceptionMessageMatches('/Expected field type .*TextLine\\\\Value/');
        $this->expectExceptionMessageMatches('/Expected field type .*TextBlock\\\\Value/');
        $this->expectExceptionMessageMatches('/Expected field type .*EmailAddress\\\\Value/');
        $this->expectExceptionMessageMatches('/but got .*Float\\\\Value/');
        $fh = new TextFieldHelper();
        $content = $this->createContentFromValue(new FloatValue());
        $fh->getString($content, 'text_field');
    }

    public function testUpdateStringCreate(): void
    {
        $fh = new TextFieldHelper();
        $struct = new ContentCreateStruct();
        $this->assertTrue($fh->updateString($struct, 'text_field', 'My Text'));
        $this->assertEquals('text_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertEquals('My Text', $struct->fields[0]->value);
    }

    public function testUpdateStringChanged(): void
    {
        $fh = new TextFieldHelper();
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromValue(new TextLineValue('Old Text'));
        $this->assertTrue($fh->updateString($struct, 'text_field', 'My Text', $content));
        $this->assertEquals('text_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertEquals('My Text', $struct->fields[0]->value);
    }

    public function testUpdateStringUnchanged(): void
    {
        $fh = new TextFieldHelper();
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromValue(new TextLineValue('My Text'));
        $this->assertFalse($fh->updateString($struct, 'text_field', 'My Text', $content));
        $this->assertCount(0, $struct->fields);
    }

    protected function createContentFromValue(Value $value): Content
    {
        $field = new Field(['value' => $value]);

        $content = $this->createMock(Content::class);
        $content->method('getField')->with('text_field')->willReturn($field);

        return $content;
    }
}