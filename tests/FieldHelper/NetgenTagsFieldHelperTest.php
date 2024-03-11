<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\FieldHelper\NetgenTagsFieldHelper;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Netgen\TagsBundle\API\Repository\TagsService;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;
use Netgen\TagsBundle\Core\FieldType\Tags\Value;
use PHPUnit\Framework\TestCase;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class NetgenTagsFieldHelperTest extends TestCase
{
    protected TagsService $tagsService;

    public function setUp(): void
    {
        $this->tagsService = $this->getMockBuilder(TagsService::class)->getMock();
        $this->fh = new NetgenTagsFieldHelper($this->tagsService);
    }

    public function testGetTag(): void
    {
        $content = $this->createContentFromTags(['Lorem', 'ipsum']);
        $tags = $this->fh->getTags($content, 'tags_field');
        $this->assertCount(2, $tags);
        $this->assertSame('Lorem', $tags[0]->keyword);
        $this->assertSame('ipsum', $tags[1]->keyword);
    }

    public function testGetFirstTag(): void
    {
        $content = $this->createContentFromTags(['Lorem', 'ipsum']);
        $tag = $this->fh->getFirstTag($content, 'tags_field');
        $this->assertSame('Lorem', $tag->getKeyword());
    }


    protected function createContentFromTags(array $tags): Content
    {
        $tagObjs = [];
        foreach($tags as $tag) {
            $tagObjs[] = new Tag(['keywords' => ['ger-DE' => $tag],'mainLanguageCode' => 'ger-DE']);
        }
        $field = new Field(['value' => new Value($tagObjs)]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('tags_field')->willReturn($field);
        $content->method('getVersionInfo')->willReturn(new VersionInfo());

        return $content;
    }
}
