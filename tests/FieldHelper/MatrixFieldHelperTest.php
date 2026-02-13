<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\FieldHelper\MatrixFieldHelper;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\FieldTypeMatrix\FieldType\Value as MatrixValue;
use PHPUnit\Framework\TestCase;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class MatrixFieldHelperTest extends TestCase
{
    /**
     * @dataProvider isEmptyProvider
     */
    public function testIsEmpty(MatrixValue $value, bool $expected): void
    {
        $fh = new MatrixFieldHelper();
        $content = $this->createContentFromValue($value);
        $this->assertSame($expected, $fh->isEmpty($content, 'matrix_field'));
    }


    public function isEmptyProvider(): array
    {
        return [
            [new MatrixValue(), true],
            [new MatrixValue([]), true],
            [new MatrixValue([new MatrixValue\Row(['x'])]), false],
        ];
    }

    protected function createContentFromValue(MatrixValue $value): Content
    {
        $field = new Field(['value' => $value]);

        $content = $this->createMock(Content::class);
        $content->method('getField')->with('matrix_field')->willReturn($field);

        return $content;
    }
}
