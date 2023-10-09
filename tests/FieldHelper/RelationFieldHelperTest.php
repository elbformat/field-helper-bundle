<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Elbformat\FieldHelperBundle\FieldHelper\RelationFieldHelper;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\FieldType\Float\Value as FloatValue;
use eZ\Publish\Core\FieldType\Relation\Value as RelationValue;
use eZ\Publish\Core\FieldType\RelationList\Value as RelationListValue;
use eZ\Publish\Core\FieldType\Value;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\HttpCache\ResponseTagger\ResponseTagger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class RelationFieldHelperTest extends TestCase
{
    protected RelationFieldHelper $fieldHelper;

    protected ContentService $contentSvc;

    protected ResponseTagger $reponseTagger;

    protected LoggerInterface $logger;

    public function setUp(): void
    {
        $this->contentSvc = $this->createMock(ContentService::class);
        $repo = $this->createMock(Repository::class);
        $repo->method('getContentService')->willReturn($this->contentSvc);
        $repo->method('sudo')->willReturnCallback(function ($arg) use ($repo) {
            return $arg($repo);
        });

        $this->reponseTagger = $this->createMock(ResponseTagger::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->fieldHelper = new RelationFieldHelper($repo, $this->reponseTagger, $this->logger);
    }

    public function testGetName(): void
    {
        $this->assertSame('Elbformat\FieldHelperBundle\FieldHelper\RelationFieldHelper', RelationFieldHelper::getName());
    }

    /** @dataProvider getContentsProvider */
    public function testGetContentsRelationList(array $contentIds): void
    {
        $targetContents = [];
        $idArrayArray = [];
        $idStringArray = [];
        foreach ($contentIds as $contentId) {
            $targetContents[] = new Content(['versionInfo' => new VersionInfo(['contentInfo' => new ContentInfo()])]);
            $idArrayArray[] = [$contentId];
            $idStringArray[] = (string) $contentId;
        }
        $this->contentSvc->method('loadContent')->withConsecutive(...$idArrayArray)->willReturnOnConsecutiveCalls(...$targetContents);
        $value = new RelationListValue($idStringArray);

        $content = $this->createContentFromValue($value);
        $getContents = $this->fieldHelper->getContents($content, 'relation_field');
        $this->assertCount(count($contentIds), $getContents);
        $this->assertSame($targetContents, $getContents);
    }

    /** @dataProvider getContentsProvider */
    public function testGetContentsRelation(array $contentIds): void
    {
        $targetContents = [];
        $idArrayArray = [];
        $idStringArray = [];
        foreach ($contentIds as $contentId) {
            $targetContents[] = new Content(['versionInfo' => new VersionInfo(['contentInfo' => new ContentInfo()])]);
            $idArrayArray[] = [$contentId];
            $idStringArray[] = (string) $contentId;
        }
        $this->contentSvc->method('loadContent')->withConsecutive(...$idArrayArray)->willReturnOnConsecutiveCalls(...$targetContents);
        $value = new RelationValue($idStringArray[0] ?? null);

        $content = $this->createContentFromValue($value);
        $getContents = $this->fieldHelper->getContents($content, 'relation_field');
        $this->assertCount(count($contentIds) ? 1 : 0, $getContents);
        $this->assertSame($targetContents[0] ?? null, $getContents[0] ?? null);
    }

    /** @dataProvider getContentsProvider */
    public function testGetOneContentRelationList(array $contentIds): void
    {
        $targetContents = [];
        $idArrayArray = [];
        $idStringArray = [];
        foreach ($contentIds as $contentId) {
            $targetContents[] = new Content(['versionInfo' => new VersionInfo(['contentInfo' => new ContentInfo()])]);
            $idArrayArray[] = [$contentId];
            $idStringArray[] = (string) $contentId;
        }
        $this->contentSvc->method('loadContent')->withConsecutive(...$idArrayArray)->willReturnOnConsecutiveCalls(...$targetContents);
        $value = new RelationListValue($idStringArray);

        $content = $this->createContentFromValue($value);
        $getContent = $this->fieldHelper->getOneContent($content, 'relation_field');
        $this->assertSame($targetContents[0] ?? null, $getContent);
    }

    /** @dataProvider getContentsProvider */
    public function testGetOneContentRelation(array $contentIds): void
    {
        $targetContents = [];
        $idArrayArray = [];
        $idStringArray = [];
        foreach ($contentIds as $contentId) {
            $targetContents[] = new Content(['versionInfo' => new VersionInfo(['contentInfo' => new ContentInfo()])]);
            $idArrayArray[] = [$contentId];
            $idStringArray[] = (string) $contentId;
        }
        $this->contentSvc->method('loadContent')->withConsecutive(...$idArrayArray)->willReturnOnConsecutiveCalls(...$targetContents);
        $value = new RelationValue($idStringArray[0] ?? null);

        $content = $this->createContentFromValue($value);
        $getContent = $this->fieldHelper->getOneContent($content, 'relation_field');
        $this->assertSame($targetContents[0] ?? null, $getContent);
    }

    public function getContentsProvider(): array
    {
        return [
            [[111]],
            [[111, 222]],
            [[]],
        ];
    }

    public function testGetContentsFieldNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $content = $this->createMock(Content::class);
        $this->fieldHelper->getContents($content, 'not_a_relation_field');
    }

    public function testGetOneContentFieldNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $content = $this->createMock(Content::class);
        $this->fieldHelper->getOneContent($content, 'not_a_relation_field');
    }

    public function testGetRelationInvalidFieldType(): void
    {
        $this->expectException(InvalidFieldTypeException::class);
        $this->expectExceptionMessageMatches('/Expected field type .*RelationList\\\\Value/');
        $this->expectExceptionMessageMatches('/Expected field type .*Relation\\\\Value/');
        $this->expectExceptionMessageMatches('/but got .*Float\\\\Value/');
        $content = $this->createContentFromValue(new FloatValue(0));
        $this->fieldHelper->getContents($content, 'relation_field');
    }

    public function testLoggingOnLoadException(): void
    {
        $content = $this->createContentFromValue(new RelationValue(111));
        $this->contentSvc->expects($this->once())->method('loadContent')->willThrowException(new NotFoundException('content', 111));
        $this->logger->expects($this->once())->method('error')->with('Error loading related content');
        $this->fieldHelper->getContents($content, 'relation_field');
    }

    /** @dataProvider updateRelationCreateProvider */
    public function testUpdateRelationCreate(array $ids): void
    {
        $struct = new ContentCreateStruct();
        $this->assertTrue($this->fieldHelper->updateRelation($struct, 'relation_field', $ids));
        $this->assertSame('relation_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertSame($ids, $struct->fields[0]->value);
    }

    public function updateRelationCreateProvider(): array
    {
        return [
            [[]],
            [[123]],
            [[123, "456"]],
        ];
    }

    /** @dataProvider updateRelationChangedProvider */
    public function testUpdateRelationChanged(array $oldIds, array $newIds): void
    {
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromValue(new RelationListValue($oldIds));
        $this->assertTrue($this->fieldHelper->updateRelation($struct, 'relation_field', $newIds, $content));
        $this->assertSame('relation_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertSame($newIds, $struct->fields[0]->value);
    }

    public function updateRelationChangedProvider(): array
    {
        return [
            [[], [123]],
            [[123], []],
            [[123], [456]],
        ];
    }

    /** @dataProvider updateRelationUnchangedProvider */
    public function testUpdateRelationUnchanged(array $oldIds, array $newIds): void
    {
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromValue(new RelationListValue($oldIds));
        $this->assertFalse($this->fieldHelper->updateRelation($struct, 'relation_field', $newIds, $content));
        $this->assertCount(0, $struct->fields);
    }

    public function updateRelationUnchangedProvider(): array
    {
        return [
            [[123], [123]],
            [[], []],
            [[123], ["123"]],
            [["123"], [123]],
            [["123", "456"], [456, 123]],
        ];
    }

    protected function createContentFromValue(?Value $value): Content
    {
        $field = new Field(['value' => $value]);

        $content = $this->createMock(Content::class);
        $content->method('getField')->with('relation_field')->willReturn($field);

        return $content;
    }
}
