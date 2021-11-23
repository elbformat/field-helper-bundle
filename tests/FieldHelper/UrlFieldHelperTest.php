<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Elbformat\FieldHelperBundle\FieldHelper\UrlFieldHelper;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\Url\Value as UrlValue;
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
class UrlFieldHelperTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertSame('Elbformat\FieldHelperBundle\FieldHelper\UrlFieldHelper', UrlFieldHelper::getName());
    }

    /**
     * @dataProvider getUrlProvider
     */
    public function testGetUrl(?string $inUrl, ?string $inText, ?string $expectedUrl, ?string $expectedString): void
    {
        $fh = new UrlFieldHelper();
        $content = $this->createContentFromLinkAndText($inUrl, $inText);
        $url = $fh->getUrl($content, 'url_field');
        $this->assertSame($expectedString, $url->getText());
        $this->assertSame($expectedUrl, $url->getUrl());
    }

    public function getUrlProvider(): array
    {
        return [
            [null, null, null, null],
            ['http://google.de', 'GOOGLE', 'http://google.de', 'GOOGLE'],
            [null, '', null, ''],
        ];
    }

    public function testGetUrlNull(): void
    {
        $fh = new UrlFieldHelper();
        $field = new Field(['value' => null]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('url_field')->willReturn($field);
        $url = $fh->getUrl($content, 'url_field');
        $this->assertNull($url->getText());
        $this->assertNull($url->getUrl());
    }

    public function testGetUrlFieldNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $fh = new UrlFieldHelper();
        $content = $this->createMock(Content::class);
        $fh->getUrl($content, 'not_a_url_field');
    }

    public function testGetUrlInvalidFieldType(): void
    {
        $this->expectException(InvalidFieldTypeException::class);
        $this->expectExceptionMessageMatches('/Expected field type .*Url\\\\Value/');
        $this->expectExceptionMessageMatches('/but got .*Float\\\\Value/');
        $fh = new UrlFieldHelper();
        $field = new Field(['value' => new FloatValue(0)]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('url_field')->willReturn($field);
        $fh->getUrl($content, 'url_field');
    }

    /** @dataProvider updateUrlCreateProvider */
    public function testUpdateUrlCreate(?string $url, ?string $text): void
    {
        $fh = new UrlFieldHelper();
        $struct = new ContentCreateStruct();
        $this->assertTrue($fh->updateUrl($struct, 'url_field', $url, $text));
        $this->assertSame('url_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertSame($url, $struct->fields[0]->value->link);
        $this->assertSame($text, $struct->fields[0]->value->text);
    }

    public function updateUrlCreateProvider(): array
    {
        return [
            ['http://google.de', 'google'],
            ['http://google.de', null],
            [null, 'google'],
            [null, null],
        ];
    }

    /** @dataProvider updateStringChangedProvider */
    public function testUpdateStringChanged(?string $oldUrl, ?string $oldText, ?string $newUrl, ?string $newText): void
    {
        $fh = new UrlFieldHelper();
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromLinkAndText($oldUrl, $oldText);
        $this->assertTrue($fh->updateUrl($struct, 'url_field', $newUrl, $newText, $content));
        $this->assertSame('url_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertSame($newUrl, $struct->fields[0]->value->link);
        $this->assertSame($newText, $struct->fields[0]->value->text);
    }

    public function updateStringChangedProvider(): array
    {
        return [
            ['http://google.de', 'google', 'http://google.de', 'My Text'],
            ['http://google.de', 'google', 'https://google.de', 'google'],
            ['http://google.de', null, 'http://google.de', 'My Text'],
            [null, 'google', 'https://google.de', 'google'],
            [null, null, 'https://google.de', null],
            [null, null, null, 'google'],
        ];
    }

    /** @dataProvider updateStringUnchangedProvider */
    public function testUpdateStringUnchanged(?string $oldUrl, ?string $oldText, ?string $newUrl, ?string $newText): void
    {
        $fh = new UrlFieldHelper();
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromLinkAndText($oldUrl, $oldText);
        $this->assertFalse($fh->updateUrl($struct, 'url_field', $newUrl, $newText, $content));
        $this->assertCount(0, $struct->fields);
    }

    public function updateStringUnchangedProvider(): array
    {
        return [
            ['http://google.de', 'google', 'http://google.de', 'google'],
            ['http://google.de', null, 'http://google.de', null],
            [null, 'google', null, 'google'],
            [null, null, null, null],
        ];
    }

    protected function createContentFromLinkAndText(?string $link, ?string $text): Content
    {
        $field = new Field(['value' => new UrlValue($link, $text)]);

        $content = $this->createMock(Content::class);
        $content->method('getField')->with('url_field')->willReturn($field);

        return $content;
    }
}
