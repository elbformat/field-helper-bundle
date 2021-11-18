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
use eZ\Publish\Core\Repository\Values\Content\Content;
use PHPUnit\Framework\TestCase;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class TextFieldHelperTest extends TestCase
{
    public function testGetName() {
        $this->assertSame('Elbformat\FieldHelperBundle\FieldHelper\TextFieldHelper', TextFieldHelper::getName());
    }

    /**
     * @dataProvider getStringProvider
     */
    public function testGetString($value, $expectedString)
    {
        $fh = new TextFieldHelper();
        $content = $this->createContentFromValue($value);
        $this->assertSame($expectedString, $fh->getString($content, 'text_field'));
    }

    /**
     * @dataProvider getStringProvider
     */
    public function testGetOptionalString($value, $expectedString)
    {
        $fh = new TextFieldHelper();
        $content = $this->createContentFromValue($value);
        $this->assertSame($expectedString, $fh->getOptionalString($content, 'text_field'));
    }

    public function getStringProvider(): array {
        return [
            [new TextLineValue('My Text'),'My Text'],
            [new TextBlockValue('My Text'),'My Text'],
            [new EmailAddressValue('test@email.de'),'test@email.de'],
            [new TextLineValue(''),''],
        ];
    }

    public function testGetStringNull()
    {
        $this->expectException(NotSetException::class);
        $fh = new TextFieldHelper();
        $content = $this->createContentFromValue(new TextLineValue(null));
        $fh->getString($content, 'text_field');
    }

    public function testGetOptionalStringNull()
    {
        $fh = new TextFieldHelper();
        $content = $this->createContentFromValue(new TextLineValue(null));
        $this->assertNull($fh->getOptionalString($content, 'text_field'));
    }

    public function testGetStringFieldNotFound()
    {
        $this->expectException(FieldNotFoundException::class);
        $fh = new TextFieldHelper();
        $content = $this->createMock(Content::class);
        $fh->getString($content, 'not_a_text_field');
    }

    public function testGetStringInvalidFieldType()
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

    // @todo test update function

    protected function createContentFromValue($value): Content {
        $field = new Field(['value' => $value]);

        $content = $this->createMock(Content::class);
        $content->method('getField')->with('text_field')->willReturn($field);

        return $content;
    }
}