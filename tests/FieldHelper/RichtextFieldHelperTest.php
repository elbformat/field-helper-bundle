<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\FieldHelper\RichtextFieldHelper;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\Value;
use Ibexa\Core\Helper\FieldHelper;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\FieldTypeRichText\FieldType\RichText\Value as RichTextValue;
use Ibexa\Contracts\FieldTypeRichText\RichText\Converter;
use PHPUnit\Framework\TestCase;

/**
 * @author Ronny Gericke <ronny.gericke@elbformat.de>
 */
class RichtextFieldHelperTest extends TestCase
{
    private const FIELD_NAME = 'richtext_field';

    protected RichtextFieldHelper $richtextFieldHelper;

    public function setUp(): void
    {
        $fieldHelper = $this->createMock(FieldHelper::class);
        $fieldHelper->method('isFieldEmpty')->willReturnCallback([$this, 'isEmptyField']);
        $converter = $this->createMock(Converter::class);

        $this->richtextFieldHelper = new RichtextFieldHelper($fieldHelper, $converter);
    }

    public function testGetName(): void
    {
        $this->assertSame('Elbformat\FieldHelperBundle\FieldHelper\RichtextFieldHelper', RichtextFieldHelper::getName());
    }

    public function testGetXml(): void
    {
        $value = $this->getXmlDomFromString('some text');
        $content = $this->createContentFromValue(new RichTextValue($value));

        $this->assertSame(
            $value,
            $this->richtextFieldHelper->getXml($content, self::FIELD_NAME)
        );
    }

    public function testIsEmpty(): void
    {
        $content = $this->createContentFromValue(new RichTextValue(null));
        $this->assertTrue($this->richtextFieldHelper->isEmpty($content, self::FIELD_NAME));
    }

    public function testIsNotEmpty(): void
    {
        $value = $this->getXmlDomFromString('not empty');
        $content = $this->createContentFromValue(new RichTextValue($value));
        $this->assertFalse($this->richtextFieldHelper->isEmpty($content, self::FIELD_NAME));
    }

    public function isEmptyField(Content $content, string $fieldName): bool
    {
        $value = $content->getField($fieldName)->value;
        if (null === $value->xml) {
            return false;
        }

        return !$value->xml->documentElement->hasChildNodes();
    }

    /**
     * @dataProvider getUpdateValues
     */
    public function testUpdateXml(?\DOMDocument $newValue, bool $expectedResult): void
    {
        $struct = new ContentUpdateStruct();
        $value = new RichTextValue(
            $this->getXmlDomFromString('initial value')
        );
        $content = $this->createContentFromValue($value);

        $this->assertSame(
            $expectedResult,
            $this->richtextFieldHelper->updateXml($struct, self::FIELD_NAME, $newValue, $content)
        );
    }

    public function getUpdateValues(): array
    {
        return [
            [$this->getXmlDomFromString('initial value'), false],
            [$this->getXmlDomFromString('other value'), true],
            [null, true],
        ];
    }

    protected function getXmlDomFromString(string $str): \DOMDocument
    {
        $xmlStr = sprintf(
            '<?xml version="1.0" encoding="UTF-8"?><section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ezxhtml="http://ez.no/xmlns/ezpublish/docbook/xhtml" xmlns:ezcustom="http://ez.no/xmlns/ezpublish/docbook/custom" version="5.0-variant ezpublish-1.0"><para>%s</para></section>',
            $str
        );
        $dom = new \DOMDocument();
        $dom->loadXML($xmlStr);

        return $dom;
    }

    protected function createContentFromValue(Value $value): Content
    {
        $field = new Field(['value' => $value]);

        $content = $this->createMock(Content::class);
        $content->method('getField')->with(self::FIELD_NAME)->willReturn($field);

        return $content;
    }
}
