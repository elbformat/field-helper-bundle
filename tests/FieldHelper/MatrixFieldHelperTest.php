<?php

declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Elbformat\FieldHelperBundle\FieldHelper\MatrixFieldHelper;
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
use EzSystems\EzPlatformMatrixFieldtype\FieldType\Value as MatrixValue;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class MatrixFieldHelperTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertSame('Elbformat\FieldHelperBundle\FieldHelper\MatrixFieldHelper', MatrixFieldHelper::getName());
    }

    /** @dataProvider getArrayProvider */
    public function testGetArray(array $in, array $out): void
    {
        $fh = new MatrixFieldHelper();
        $content = $this->createContent($in);
        $this->assertSame($out, $fh->getArray($content, 'matrix_field'));
    }

    public function getArrayProvider(): array
    {
        return [
            [$this->getThreeTimesThreeValue(), [['A1', 'B1', 'C1'], ['A2', 'B2', 'C2'], ['A3', 'B3', 'C3']]],
        ];
    }

    /** @dataProvider getAssocProvider */
    public function testGetAssoc(array $in, array $out): void
    {
        $fh = new MatrixFieldHelper();
        $content = $this->createContent($in);
        $this->assertSame($out, $fh->getAssoc($content, 'matrix_field'));
    }

    public function getAssocProvider(): array
    {
        return [
            [
                $this->getThreeTimesThreeValue(),
                [['A1' => 'A2', 'B1' => 'B2', 'C1' => 'C2'], ['A1' => 'A3', 'B1' => 'B3', 'C1' => 'C3']],
            ],
        ];
    }


//    public function testGetStringFieldNotFound(): void
//    {
//        $this->expectException(FieldNotFoundException::class);
//        $fh = new MatrixFieldHelper();
//        $content = $this->createMock(Content::class);
//        $fh->getString($content, 'not_a_matrix_field');
//    }
//
//    public function testGetStringInvalidFieldType(): void
//    {
//        $this->expectException(InvalidFieldTypeException::class);
//        $this->expectExceptionMessageMatches('/Expected field type .*TextLine\\\\Value/');
//        $this->expectExceptionMessageMatches('/Expected field type .*TextBlock\\\\Value/');
//        $this->expectExceptionMessageMatches('/Expected field type .*EmailAddress\\\\Value/');
//        $this->expectExceptionMessageMatches('/but got .*Float\\\\Value/');
//        $fh = new MatrixFieldHelper();
//        $content = $this->createContent(new FloatValue());
//        $fh->getString($content, 'matrix_field');
//    }
//
//    public function testUpdateStringCreate(): void
//    {
//        $fh = new MatrixFieldHelper();
//        $struct = new ContentCreateStruct();
//        $this->assertTrue($fh->updateString($struct, 'matrix_field', 'My Text'));
//        $this->assertEquals('matrix_field', $struct->fields[0]->fieldDefIdentifier);
//        $this->assertEquals('My Text', $struct->fields[0]->value);
//    }
//
//    public function testUpdateStringChanged(): void
//    {
//        $fh = new MatrixFieldHelper();
//        $struct = new ContentUpdateStruct();
//        $content = $this->createContent(new TextLineValue('Old Text'));
//        $this->assertTrue($fh->updateString($struct, 'matrix_field', 'My Text', $content));
//        $this->assertEquals('matrix_field', $struct->fields[0]->fieldDefIdentifier);
//        $this->assertEquals('My Text', $struct->fields[0]->value);
//    }
//
//    public function testUpdateStringUnchanged(): void
//    {
//        $fh = new MatrixFieldHelper();
//        $struct = new ContentUpdateStruct();
//        $content = $this->createContent(new TextLineValue('My Text'));
//        $this->assertFalse($fh->updateString($struct, 'matrix_field', 'My Text', $content));
//        $this->assertCount(0, $struct->fields);
//    }

    protected function getThreeTimesThreeValue(): array
    {
        return [
            new MatrixValue\Row(['A1', 'B1', 'C1']),
            new MatrixValue\Row(['A2', 'B2', 'C2']),
            new MatrixValue\Row(['A3', 'B3', 'C3']),
        ];
    }

    protected function createContent(array $data): Content
    {
        $field = new Field(['value' => new MatrixValue($data)]);

        $content = $this->createMock(Content::class);
        $content->method('getField')->with('matrix_field')->willReturn($field);

        return $content;
    }
}
