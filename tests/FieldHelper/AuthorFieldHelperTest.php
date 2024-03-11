<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Elbformat\FieldHelperBundle\FieldHelper\AuthorFieldHelper;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\Author\Author;
use Ibexa\Core\FieldType\Author\Value;
use Ibexa\Core\FieldType\Float\Value as FloatValue;
use Ibexa\Core\Repository\Values\Content\Content;
use PHPUnit\Framework\TestCase;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class AuthorFieldHelperTest extends TestCase
{
    public function testGetValues(): void
    {
        $fh = new AuthorFieldHelper();
        $content = $this->createContentFromAuthors([[1, 'Author 1', 'author@format-h.com']]);
        $collection = $fh->getValues($content, 'author_field');
        $this->assertSame(1, $collection->count());
        /** @var Author $author1 */
        $author1 = $collection->offsetGet(0);
        $this->assertSame(1, $author1->id);
        $this->assertSame('Author 1', $author1->name);
        $this->assertSame('author@format-h.com', $author1->email);
    }

    public function testGetValuesFieldNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $fh = new AuthorFieldHelper();
        $content = $this->createMock(Content::class);
        $fh->getValues($content, 'author_field');
    }

    public function testGetValuesInvalidFieldType(): void
    {
        $this->expectException(InvalidFieldTypeException::class);
        $this->expectExceptionMessageMatches('/Expected field type .*Author\\\\Value/');
        $this->expectExceptionMessageMatches('/but got .*Float\\\\Value/');
        $fh = new AuthorFieldHelper();
        $field = new Field(['value' => new FloatValue(1.0)]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('author_field')->willReturn($field);
        $fh->getValues($content, 'author_field');
    }

    /** @dataProvider getNamesProvider */
    public function testGetNames(array $data, string $expected): void
    {
        $fh = new AuthorFieldHelper();
        $content = $this->createContentFromAuthors($data);
        $names = $fh->getNames($content, 'author_field');
        $this->assertSame($expected, $names);
    }

    public function getNamesProvider(): array
    {
        return [
            [[[1, 'Author 1', 'author@format-h.com']], 'Author 1'],
            [[[1, 'Author 1', 'author@format-h.com'], [2, 'Author 2', 'author2@format-h.com']], 'Author 1, Author 2'],
            [[], ''],
        ];
    }

    protected function createContentFromAuthors(array $authors): Content
    {
        $authorObjects = [];
        foreach ($authors as [$id, $name, $email]) {
            $authorObjects[] = new Author(['id' => $id, 'name' => $name, 'email' => $email]);
        }
        $field = new Field(['value' => new Value($authorObjects)]);
        $content = $this->createMock(Content::class);
        $content->method('getField')->with('author_field')->willReturn($field);

        return $content;
    }
}
