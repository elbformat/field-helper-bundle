<?php declare(strict_types=1);

namespace Elbformat\FieldHelperBundle\Tests\FieldHelper;

use Elbformat\FieldHelperBundle\Exception\FieldNotFoundException;
use Elbformat\FieldHelperBundle\Exception\InvalidFieldTypeException;
use Elbformat\FieldHelperBundle\Exception\NotSetException;
use Elbformat\FieldHelperBundle\FieldHelper\DateTimeFieldHelper;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\FieldType\Date\Value as DateValue;
use eZ\Publish\Core\FieldType\DateAndTime\Value as DateTimeValue;
use eZ\Publish\Core\FieldType\Float\Value as FloatValue;
use eZ\Publish\Core\FieldType\Time\Value as TimeValue;
use eZ\Publish\Core\FieldType\Value;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\ContentCreateStruct;
use eZ\Publish\Core\Repository\Values\Content\ContentUpdateStruct;
use PHPUnit\Framework\TestCase;

/**
 * @author Hannes Giesenow <hannes.giesenow@elbformat.de>
 */
class DateTimeFieldHelperTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertSame('Elbformat\FieldHelperBundle\FieldHelper\DateTimeFieldHelper', DateTimeFieldHelper::getName());
    }

    /**
     * @dataProvider getDateTimeProvider
     */
    public function testGetDateTime(Value $value, ?string $expectedDate, ?string $expectedTime): void
    {
        $fh = new DateTimeFieldHelper();
        $content = $this->createContentFromValue($value);
        $datetime = $fh->getDateTime($content, 'date_field');
        $this->assertSame($expectedDate, $datetime->format('Y-m-d'));
        $this->assertSame($expectedTime, $datetime->format('H:i:s'));
    }

    public function getDateTimeProvider(): array
    {
        $current = new \DateTime();

        return [
            [new DateValue(new \DateTime('2021-11-11')), '2021-11-11', '00:00:00'],
            [new DateTimeValue(new \DateTime('2021-11-11')), '2021-11-11', '00:00:00'],
            [new TimeValue(1636629071), $current->format('Y-m-d'), '11:11:11'],
        ];
    }

    /**
     * @dataProvider getDateTimeNullProvider
     */
    public function testGetDateTimeNull(Value $value): void
    {
        $fh = new DateTimeFieldHelper();
        $content = $this->createContentFromValue($value);
        $this->assertNull($fh->getDateTime($content, 'date_field'));
    }

    public function getDateTimeNullProvider(): array
    {
        return [
            [new DateValue(null)],
            [new DateTimeValue(null)],
            [new TimeValue(null)],
        ];
    }

    public function testGetDateTimeFieldNotFound(): void
    {
        $this->expectException(FieldNotFoundException::class);
        $fh = new DateTimeFieldHelper();
        $content = $this->createMock(Content::class);
        $fh->getDateTime($content, 'not_a_date_field');
    }

    public function testGetDateTimeInvalidFieldType(): void
    {
        $this->expectException(InvalidFieldTypeException::class);
        $this->expectExceptionMessageMatches('/Expected field type .*Date\\\\Value/');
        $this->expectExceptionMessageMatches('/Expected field type .*Time\\\\Value/');
        $this->expectExceptionMessageMatches('/Expected field type .*DateAndTime\\\\Value/');
        $this->expectExceptionMessageMatches('/but got .*Float\\\\Value/');
        $fh = new DateTimeFieldHelper();
        $content = $this->createContentFromValue(new FloatValue());
        $fh->getDateTime($content, 'date_field');
    }

    public function testUpdateDateTimeCreate(): void
    {
        $fh = new DateTimeFieldHelper();
        $struct = new ContentCreateStruct();
        $this->assertTrue($fh->updateDateTime($struct, 'date_field', new \DateTime('2021-11-11')));
        $this->assertEquals('date_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertEquals('2021-11-11', $struct->fields[0]->value->format('Y-m-d'));
    }

    /** @dataProvider updateDateTimeChangedProvider */
    public function testUpdateDateTimeChanged(Value $existingValue, \DateTimeInterface $newDate): void
    {
        $fh = new DateTimeFieldHelper();
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromValue($existingValue);
        $this->assertTrue($fh->updateDateTime($struct, 'date_field', $newDate, $content));
        $this->assertEquals('date_field', $struct->fields[0]->fieldDefIdentifier);
        $this->assertEquals($newDate->format('Y-m-d H:i:s'), $struct->fields[0]->value->format('Y-m-d H:i:s'));
    }

    public function updateDateTimeChangedProvider(): array {
        return [
            [new DateValue(new \DateTime('2021-11-12')), new \DateTime('2021-11-11')],
            [new DateTimeValue(new \DateTime('2021-11-12')), new \DateTimeImmutable('2021-11-11')],
            [new TimeValue(11), new \DateTimeImmutable('2021-11-11 00:00:12')],
        ];
    }

    public function testUpdateDateTimeUnchanged(): void
    {
        $fh = new DateTimeFieldHelper();
        $struct = new ContentUpdateStruct();
        $content = $this->createContentFromValue(new DateValue(new \DateTime('2021-11-11')));
        $this->assertFalse($fh->updateDateTime($struct, 'date_field', new \DateTimeImmutable('2021-11-11'), $content));
        $this->assertCount(0, $struct->fields);
    }

    public function updateDateTimeUnchangedProvider(): array {
        return [
            [new DateValue(new \DateTime('2021-11-12')), new \DateTime('2021-11-11')],
            [new DateTimeValue(new \DateTime('2021-11-12')), new \DateTimeImmutable('2021-11-11')],
            [new TimeValue(11), new \DateTimeImmutable('2021-11-11 00:00:12')],
        ];
    }

    protected function createContentFromValue(Value $value): Content
    {
        $field = new Field(['value' => $value]);

        $content = $this->createMock(Content::class);
        $content->method('getField')->with('date_field')->willReturn($field);

        return $content;
    }
}