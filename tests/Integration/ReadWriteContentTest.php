<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\Integration;

use Doctrine\DBAL\Connection;
use Elbformat\FieldHelperBundle\Registry\RegistryInterface;
use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Repository;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\API\Repository\Values\Content\ContentUpdateStruct;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeCreateStruct;
use EzSystems\DoctrineSchema\API\Builder\SchemaBuilder;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class ReadWriteContentTest extends KernelTestCase
{
    protected Repository $repo;
    protected RegistryInterface $fhReg;
    protected static ?ContentType $contentType = null;
    protected ContentService $contentService;

    public function setUp(): void
    {
        parent::setUp();
        $this->repo = $this->containerInstance->get('ezpublish.api.repository');
        $this->fhReg = $this->containerInstance->get(RegistryInterface::class);
        $this->contentService = $this->repo->getContentService();
        // Create database structure and content-type only once
        if (null === self::$contentType) {
            $this->initDatabase();
            self::$contentType = $this->createContentType();
        }
    }

    public function testBoolean(): void
    {
        $fhBool = $this->fhReg->getBoolFieldHelper();

        // Create content
        $newStruct = $this->contentService->newContentCreateStruct(self::$contentType, 'eng-GB');
        $this->assertTrue($fhBool->updateBool($newStruct, 'boolean', true));
        $content = $this->createContent($newStruct);

        // Read content
        $this->assertTrue($fhBool->getBool($content, 'boolean'));

        // Update content (unchanged)
        $updStruct = $this->contentService->newContentUpdateStruct();
        $this->assertFalse($fhBool->updateBool($updStruct, 'boolean', true, $content));
        $this->assertTrue($fhBool->updateBool($updStruct, 'boolean', false, $content));
        $content = $this->updateContent($content, $updStruct);
        $this->assertFalse($fhBool->getBool($content, 'boolean'));
    }

    public function testDateTime(): void
    {
        $fhDate = $this->fhReg->getDateTimeFieldHelper();

        // Create content
        $newStruct = $this->contentService->newContentCreateStruct(self::$contentType, 'eng-GB');
        $this->assertTrue($fhDate->updateDateTime($newStruct, 'datetime', new \DateTimeImmutable('2021-12-21 11:11:11')));
        $content = $this->createContent($newStruct);

        // Read content
        $this->assertEquals(new \DateTimeImmutable('2021-12-21 11:11:11'), $fhDate->getDateTime($content, 'datetime'));

        // Update content (unchanged)
        $updStruct = $this->contentService->newContentUpdateStruct();
        $this->assertFalse($fhDate->updateDateTime($updStruct, 'datetime', new \DateTimeImmutable('2021-12-21 11:11:11'), $content));
        $this->assertTrue($fhDate->updateDateTime($updStruct, 'datetime', new \DateTimeImmutable('2021-12-21 11:11:10'), $content));
        $content = $this->updateContent($content, $updStruct);
        $this->assertEquals(new \DateTimeImmutable('2021-12-21 11:11:10'), $fhDate->getDateTime($content, 'datetime'));
    }

    public function testDate(): void
    {
        $fhDate = $this->fhReg->getDateTimeFieldHelper();

        // Create content
        $newStruct = $this->contentService->newContentCreateStruct(self::$contentType, 'eng-GB');
        $this->assertTrue($fhDate->updateDateTime($newStruct, 'date', new \DateTimeImmutable('2021-12-21 11:11:11')));
        $content = $this->createContent($newStruct);

        // Read content
        $this->assertEquals(new \DateTimeImmutable('2021-12-21 0:0:0'), $fhDate->getDateTime($content, 'date'));

        // Update content (unchanged)
        $updStruct = $this->contentService->newContentUpdateStruct();
        $this->assertFalse($fhDate->updateDateTime($updStruct, 'date', new \DateTimeImmutable('2021-12-21 12:12:12'), $content));
        $this->assertTrue($fhDate->updateDateTime($updStruct, 'date', new \DateTimeImmutable('2021-12-22 11:11:11'), $content));
        $content = $this->updateContent($content, $updStruct);
        $this->assertEquals(new \DateTimeImmutable('2021-12-22 0:0:0'), $fhDate->getDateTime($content, 'date'));
    }

    public function testEmailAddress(): void
    {
        $fhText = $this->fhReg->getTextFieldHelper();

        // Create content
        $newStruct = $this->contentService->newContentCreateStruct(self::$contentType, 'eng-GB');
        $this->assertTrue($fhText->updateString($newStruct, 'email', 'test@elbformat.de'));
        $content = $this->createContent($newStruct);

        // Read content
        $this->assertSame('test@elbformat.de', $fhText->getString($content, 'email'));

        // Update content (unchanged)
        $updStruct = $this->contentService->newContentUpdateStruct();
        $this->assertFalse($fhText->updateString($updStruct, 'email', 'test@elbformat.de', $content));
        $this->assertTrue($fhText->updateString($updStruct, 'email', 'test2@elbformat.de', $content));
        $content = $this->updateContent($content, $updStruct);
        $this->assertSame('test2@elbformat.de', $fhText->getString($content, 'email'));
    }

    public function testFloat(): void
    {
        $fhNum = $this->fhReg->getNumberFieldHelper();

        // Create content
        $newStruct = $this->contentService->newContentCreateStruct(self::$contentType, 'eng-GB');
        $this->assertTrue($fhNum->updateFloat($newStruct, 'float', 1.23));
        $content = $this->createContent($newStruct);

        // Read content
        $this->assertSame(1.23, $fhNum->getFloat($content, 'float'));
        $this->assertSame(1, $fhNum->getInteger($content, 'float'));

        // Update content (unchanged)
        $updStruct = $this->contentService->newContentUpdateStruct();
        $this->assertFalse($fhNum->updateFloat($updStruct, 'float', 1.23, $content));
        $this->assertTrue($fhNum->updateFloat($updStruct, 'float', 2.34, $content));
        $content = $this->updateContent($content, $updStruct);
        $this->assertSame(2.34, $fhNum->getFloat($content, 'float'));
        $this->assertSame(2, $fhNum->getInteger($content, 'float'));
    }

    public function testInteger(): void
    {
        $fhNum = $this->fhReg->getNumberFieldHelper();

        // Create content
        $newStruct = $this->contentService->newContentCreateStruct(self::$contentType, 'eng-GB');
        $this->assertTrue($fhNum->updateInteger($newStruct, 'integer', 1));
        $content = $this->createContent($newStruct);

        // Read content
        $this->assertSame(1, $fhNum->getInteger($content, 'integer'));
        $this->assertSame(1.0, $fhNum->getFloat($content, 'integer'));

        // Update content (unchanged)
        $updStruct = $this->contentService->newContentUpdateStruct();
        $this->assertFalse($fhNum->updateInteger($updStruct, 'integer', 1, $content));
        $this->assertTrue($fhNum->updateInteger($updStruct, 'integer', 2, $content));
        $content = $this->updateContent($content, $updStruct);
        $this->assertSame(2, $fhNum->getInteger($content, 'integer'));
        $this->assertSame(2.0, $fhNum->getFloat($content, 'integer'));
    }

    public function testObjectRelationlist(): void
    {
        $fhRel = $this->fhReg->getRelationFieldHelper();

        // Dummy content to relate to
        $newTargetStruct = $this->contentService->newContentCreateStruct(self::$contentType, 'eng-GB');
        $target1 = $this->createContent($newTargetStruct);
        $target2 = $this->createContent($newTargetStruct);

        // Create content
        $newStruct = $this->contentService->newContentCreateStruct(self::$contentType, 'eng-GB');
        $this->assertTrue($fhRel->updateRelation($newStruct, 'objectrelationlist', [$target1->id]));
        $content = $this->createContent($newStruct);

        // Read content
        $this->assertEquals($target1->id, $fhRel->getOneContent($content, 'objectrelationlist')->id);
        $targets = $fhRel->getContents($content, 'objectrelationlist');
        $this->assertCount(1, $targets);
        $this->assertEquals($target1->id, $targets[0]->id);

        // Update content (unchanged)
        $updStruct = $this->contentService->newContentUpdateStruct();
        $this->assertFalse($fhRel->updateRelation($updStruct, 'objectrelationlist', [$target1->id], $content));
        $this->assertTrue($fhRel->updateRelation($updStruct, 'objectrelationlist', [$target2->id], $content));
        $content = $this->updateContent($content, $updStruct);
        $this->assertEquals($target2->id, $fhRel->getOneContent($content, 'objectrelationlist')->id);
        $targets = $fhRel->getContents($content, 'objectrelationlist');
        $this->assertCount(1, $targets);
        $this->assertEquals($target2->id, $targets[0]->id);
    }

    public function testText(): void
    {
        $fhText = $this->fhReg->getTextFieldHelper();

        // Create content
        $newStruct = $this->contentService->newContentCreateStruct(self::$contentType, 'eng-GB');
        $this->assertTrue($fhText->updateString($newStruct, 'text', 'testtitle'));
        $content = $this->createContent($newStruct);

        // Read content
        $this->assertSame('testtitle', $fhText->getString($content, 'text'));

        // Update content (unchanged)
        $updStruct = $this->contentService->newContentUpdateStruct();
        $this->assertFalse($fhText->updateString($updStruct, 'text', 'testtitle', $content));
        $this->assertTrue($fhText->updateString($updStruct, 'text', 'anothertitle', $content));
        $content = $this->updateContent($content, $updStruct);
        $this->assertSame('anothertitle', $fhText->getString($content, 'text'));
    }

    public function testString(): void
    {
        $fhText = $this->fhReg->getTextFieldHelper();

        // Create content
        $newStruct = $this->contentService->newContentCreateStruct(self::$contentType, 'eng-GB');
        $this->assertTrue($fhText->updateString($newStruct, 'string', 'testtitle'));
        $content = $this->createContent($newStruct);

        // Read content
        $this->assertSame('testtitle', $fhText->getString($content, 'string'));

        // Update content (unchanged)
        $updStruct = $this->contentService->newContentUpdateStruct();
        $this->assertFalse($fhText->updateString($updStruct, 'string', 'testtitle', $content));
        $this->assertTrue($fhText->updateString($updStruct, 'string', 'anothertitle', $content));
        $content = $this->updateContent($content, $updStruct);
        $this->assertSame('anothertitle', $fhText->getString($content, 'string'));
    }

    public function testTime(): void
    {
        $fhDate = $this->fhReg->getDateTimeFieldHelper();

        // Create content
        $newStruct = $this->contentService->newContentCreateStruct(self::$contentType, 'eng-GB');
        $this->assertTrue($fhDate->updateDateTime($newStruct, 'time', new \DateTimeImmutable('2021-12-21 11:11:11')));
        $content = $this->createContent($newStruct);

        // Read content
        $current = new \DateTimeImmutable();
        $this->assertEquals(new \DateTimeImmutable($current->format('Y-m-d').' 11:11:11'), $fhDate->getDateTime($content, 'time'));

        // Update content (unchanged)
        $updStruct = $this->contentService->newContentUpdateStruct();
        $this->assertFalse($fhDate->updateDateTime($updStruct, 'time', new \DateTimeImmutable('2021-12-22 11:11:11'), $content));
        $this->assertTrue($fhDate->updateDateTime($updStruct, 'time', new \DateTimeImmutable('2021-12-22 11:11:12'), $content));
        $content = $this->updateContent($content, $updStruct);
        $this->assertEquals(new \DateTimeImmutable($current->format('Y-m-d').'11:11:12'), $fhDate->getDateTime($content, 'time'));
    }

    public function testUrl(): void
    {
        $fhUrl = $this->fhReg->getUrlFieldHelper();

        // Create content
        $newStruct = $this->contentService->newContentCreateStruct(self::$contentType, 'eng-GB');
        $this->assertTrue($fhUrl->updateUrl($newStruct, 'url', 'http://google.de', 'Google'));
        $content = $this->createContent($newStruct);

        // Read content
        $url = $fhUrl->getUrl($content, 'url');
        $this->assertSame('http://google.de', $url->getUrl());
        $this->assertSame('Google', $url->getText());

        // Update content (unchanged)
        $updStruct = $this->contentService->newContentUpdateStruct();
        $this->assertFalse($fhUrl->updateUrl($updStruct, 'url', 'http://google.de', 'Google', $content));
        $this->assertTrue($fhUrl->updateUrl($updStruct, 'url', 'http://google.de/', 'Google', $content));
        $content = $this->updateContent($content, $updStruct);
        $this->assertTrue($fhUrl->updateUrl($updStruct, 'url', 'http://google.de/', 'Gooogle', $content));
        $content = $this->updateContent($content, $updStruct);
        $url = $fhUrl->getUrl($content, 'url');
        $this->assertSame('http://google.de/', $url->getUrl());
        $this->assertSame('Gooogle', $url->getText());
    }

    protected function createContentType(): ContentType
    {
        // Create content-type with one of each kind
        $repo = $this->containerInstance->get('ezpublish.api.repository');
        $ctStruct = $repo->getContentTypeService()->newContentTypeCreateStruct('test');
        $ctStruct->mainLanguageCode = 'eng-GB';
        $ctStruct->names = ['eng-GB' => 'Test'];
        $this->addField($ctStruct, 'boolean', 'ezboolean', 10);
        $this->addField($ctStruct, 'datetime', 'ezdatetime', 20);
        $this->addField($ctStruct, 'date', 'ezdate', 30);
        $this->addField($ctStruct, 'email', 'ezemail', 40);
        $this->addField($ctStruct, 'float', 'ezfloat', 50);
        $this->addField($ctStruct, 'integer', 'ezinteger', 60);
        $this->addField($ctStruct, 'objectrelation', 'ezobjectrelation', 80);
        $this->addField($ctStruct, 'objectrelationlist', 'ezobjectrelationlist', 90);
        $this->addField($ctStruct, 'text', 'eztext', 120);
        $this->addField($ctStruct, 'string', 'ezstring', 130);
        $this->addField($ctStruct, 'time', 'eztime', 140);
        $this->addField($ctStruct, 'url', 'ezurl', 150);
        $group = $repo->getContentTypeService()->loadContentTypeGroup(2);
        $repo->sudo(function (Repository $repo) use ($ctStruct, $group) {
            $draft = $repo->getContentTypeService()->createContentType($ctStruct, [$group]);
            $repo->getContentTypeService()->publishContentTypeDraft($draft);
        });

        return $repo->getContentTypeService()->loadContentTypeByIdentifier('test');
    }

    protected function createContent(ContentCreateStruct $newStruct): Content
    {
        return $this->repo->sudo(function (Repository $repo) use ($newStruct) {
            $newContent = $repo->getContentService()->createContent($newStruct);
            return $repo->getContentService()->publishVersion($newContent->versionInfo);
        });
    }

    protected function updateContent(Content $content, ContentUpdateStruct $updStruct): Content
    {
        return $this->repo->sudo(function (Repository $repo) use ($content, $updStruct) {
            $draft = $repo->getContentService()->createContentDraft($content->contentInfo);
            $repo->getContentService()->updateContent($draft->versionInfo, $updStruct);

            return $repo->getContentService()->publishVersion($draft->versionInfo);
        });
    }

    protected function addField(ContentTypeCreateStruct $struct, string $name, string $type, int $position)
    {
        $fieldStruct = $this->repo->getContentTypeService()->newFieldDefinitionCreateStruct($name, $type);
        $fieldStruct->position = $position;
        $struct->addFieldDefinition($fieldStruct);
    }

    protected function initDatabase()
    {
        // These functions are mainly adapted from CoreInstaller
        $schemaBuilder = $this->containerInstance->get(SchemaBuilder::class);
        $db = $this->containerInstance->get(Connection::class);
        $schema = $schemaBuilder->buildSchema();
        $databasePlatform = $db->getDatabasePlatform();

        // Drop all tables
        $existingSchema = $db->getSchemaManager()->createSchema();
        // reverse table order for clean-up (due to FKs)
        $tables = array_reverse($schema->getTables());
        // cleanup pre-existing database
        foreach ($tables as $table) {
            if ($existingSchema->hasTable($table->getName())) {
                $db->exec($databasePlatform->getDropTableSQL($table));
            }
        }

        // Create new schema
        $queries = $schema->toSql($databasePlatform);
        foreach ($queries as $query) {
            $db->exec($query);
        }

        // Fill up with initial data
        $queries = array_filter(preg_split('(;\\s*$)m', file_get_contents(__DIR__ . '/../../vendor/ezsystems/ezplatform-kernel/data/mysql/cleandata.sql')));
        foreach ($queries as $query) {
            $db->exec(str_replace('\"', '"', $query));
        }
    }
}
