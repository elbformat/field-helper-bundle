<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Elbformat\FieldHelperBundle\FieldHelper\ImageFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\RelationFieldHelper;
use Elbformat\FieldHelperBundle\FieldHelper\RichtextFieldHelper;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\FieldType\Float\Value as FloatValue;
use eZ\Publish\Core\FieldType\Image\Value;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\SPI\Variation\VariationHandler;
use Ibexa\Contracts\HttpCache\ResponseTagger\ResponseTagger;
use PHPUnit\Framework\TestCase;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class ImageFieldHelperTest extends TestCase
{
    protected ImageFieldHelper $fh;
    protected RichtextFieldHelper $fhRichtext;
    protected RelationFieldHelper $fhRel;
    protected Repository $repo;

    public function setUp(): void
    {
        $variationHandler = $this->getMockBuilder(VariationHandler::class)->getMock();
        $this->repo = $this->getMockBuilder(Repository::class)->getMock();
        $configResolver = $this->getMockBuilder(ConfigResolverInterface::class)->getMock();
        $configResolver->method('getParameter')->with('image_variations')->willReturn([
            'variation1' => '...',
        ]);
        $responseTagger = $this->getMockBuilder(ResponseTagger::class)->getMock();
        $this->fhRichtext = $this->getMockBuilder(RichtextFieldHelper::class)->disableOriginalConstructor()->getMock();
        $this->fhRel = $this->getMockBuilder(RelationFieldHelper::class)->disableOriginalConstructor()->getMock();
        $this->fh = new ImageFieldHelper($variationHandler, $this->repo, $configResolver, $responseTagger, $this->fhRichtext, $this->fhRel);
    }

    public function testGetImageFromImage(): void
    {
        $content = $this->createContentFromImage(alt:'cat', caption:'cap');
        $image = $this->fh->getImage($content);
        $this->assertSame('cat', $image->getAlt());
        $this->assertSame('cap', $image->getCaption());
    }

    public function testGetImageFromRelation(): void
    {
        $content = $this->createContentFromRelation(alt:'cat', caption:'cap');
        $image = $this->fh->getImage($content, 'image_field');
        $this->assertSame('cat', $image->getAlt());
        $this->assertSame('cap', $image->getCaption());
    }

    public function testGetImageFromRelationEmpty(): void
    {
        $this->fhRel->method('getOneContent')->willReturn(null);
        $field = new Field(['value' => new \eZ\Publish\Core\FieldType\RelationList\Value(['destinationContentIds' => 1])]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('image_field')->willReturn($field);

        $this->assertNull($this->fh->getImage($content, 'image_field'));
    }
    public function testGetImageFromRelationNotFound(): void
    {
        $this->fhRel->method('getOneContent')->willThrowException(new NotFoundException('content', 1));
        $field = new Field(['value' => new \eZ\Publish\Core\FieldType\RelationList\Value(['destinationContentIds' => 1])]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('image_field')->willReturn($field);

        $this->assertNull($this->fh->getImage($content, 'image_field'));
    }

    public function testGetImageFromImageAsset(): void
    {
        $content = $this->createContentFromAsset(alt:'cat', caption:'cap');
        $image = $this->fh->getImage($content, 'image_field');
        $this->assertSame('cat', $image->getAlt());
        $this->assertSame('cap', $image->getCaption());
    }

    public function testGetImageFromImageAssetEmpty(): void
    {
        $field = new Field(['value' => new \eZ\Publish\Core\FieldType\ImageAsset\Value()]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('image_field')->willReturn($field);
        $this->assertNull($this->fh->getImage($content, 'image_field'));
    }

    public function testGetFileNameFieldNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $content = $this->createMock(Content::class);
        $this->fh->getImage($content, 'image_field');
    }

    public function testGetFileNameInvalidFieldType(): void
    {
        $this->expectException(InvalidFieldTypeException::class);
        $this->expectExceptionMessageMatches('/Expected field type .*Checkbox\\\\Value/');
        $this->expectExceptionMessageMatches('/but got .*Float\\\\Value/');
        $field = new Field(['value' => new FloatValue(1.0)]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('image_field')->willReturn($field);
        $this->fh->getImage($content, 'image_field');
    }

    protected function createContentFromImage(?string $alt = null, ?string $caption = null): Content
    {
        $field = new Field(['value' => new Value(['alternativeText' => $alt])]);
        $content = $this->createMock(Content::class);
        $content->method('getContentType')->willReturn(new ContentType(['identifier' => 'image']));
        $content->method('getField')->with('image')->willReturn($field);
        if (null !== $caption) {
            $this->fhRichtext->method('getHtml')->willReturn($caption);
        }
        $content->method('getVersionInfo')->willReturn(new VersionInfo());

        return $content;
    }

    protected function createContentFromRelation(?string $alt = null, ?string $caption = null): Content
    {
        $image = $this->createContentFromImage($alt, $caption);
        $this->fhRel->method('getOneContent')->willReturn($image);
        $field = new Field(['value' => new \eZ\Publish\Core\FieldType\RelationList\Value(['destinationContentIds' => 1])]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('image_field')->willReturn($field);

        return $content;
    }

    protected function createContentFromAsset(?string $alt = null, ?string $caption = null): Content
    {
        $image = $this->createContentFromImage($alt, $caption);
        $field = new Field(['value' => new \eZ\Publish\Core\FieldType\ImageAsset\Value(1, $alt)]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('image_field')->willReturn($field);
        $contentSvc = $this->getMockBuilder(ContentService::class)->getMock();
        $contentSvc->method('loadContent')->willReturn($image);
        $this->repo->method('getContentService')->willReturn($contentSvc);
        return $content;
    }
}
