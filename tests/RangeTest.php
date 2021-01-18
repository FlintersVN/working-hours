<?php

namespace Flinters\OpenHour\Tests;

use Flinters\WorkingHours\Range;
use Flinters\WorkingHours\Time;
use PHPUnit\Framework\TestCase;

class RangeTest extends TestCase
{
    public function test_from_string()
    {
        $this->assertEquals(
            new Range(Time::fromString('08:30'), Time::fromString('17:30')),
            Range::fromString('08:30-17:30')
        );
    }

    public function test_duration()
    {
        $this->assertEquals(
            new Range(Time::fromString('08:30'), Time::fromString('17:30')),
            Range::duration(Time::fromString('08:30'), 540)
        );
    }

    public function test_contains()
    {
        $range = Range::fromString('08:30-17:30');
        $this->assertTrue($range->contains(Time::fromString('11:30')));
        $this->assertTrue($range->contains(Time::fromString('08:30')));
        $this->assertTrue($range->contains(Time::fromString('17:30')));
        $this->assertFalse($range->contains(Time::fromString('08:29')));
        $this->assertFalse($range->contains(Time::fromString('08:00')));
    }

    public function test_contains_range()
    {
        $range = Range::fromString('08:30-17:30');
        $this->assertTrue($range->containsRange(Range::fromString('03:00-12:00')));
        $this->assertTrue($range->containsRange(Range::fromString('10:30-12:00')));
        $this->assertTrue($range->containsRange(Range::fromString('12:30-18:00')));
        $this->assertFalse($range->containsRange(Range::fromString('18:00-21:00')));
    }

    public function test_merge()
    {
        $range = Range::fromString('08:30-17:30');
        $this->assertEquals('08:30-17:30', $range->merge(Range::fromString('10:30-12:30'))->toString());
        $this->assertEquals('08:30-18:30', $range->merge(Range::fromString('08:30-18:30'))->toString());
        $this->assertEquals('08:30-18:30', $range->merge(Range::fromString('09:30-18:30'))->toString());
        $this->assertEquals('07:30-18:30', $range->merge(Range::fromString('07:30-18:30'))->toString());
        $this->assertEquals('07:30-17:30', $range->merge(Range::fromString('07:30-15:30'))->toString());
    }

    public function test_break()
    {
        $range = Range::fromString('08:30-17:30');
        $this->assertEquals(
            [Range::fromString('08:30-11:30'), Range::fromString('12:45-17:30')],
            $range->break(Range::fromString('11:30-12:45'))
        );

        $range = Range::fromString('08:30-17:30');
        $this->assertEquals(
            [],
            $range->break(Range::fromString('00:00-23:59'))
        );

        $range = Range::fromString('08:30-17:30');
        $this->assertEquals(
            [Range::fromString('09:30-17:30')],
            $range->break(Range::fromString('00:00-09:30'))
        );

        $range = Range::fromString('08:30-17:30');
        $this->assertEquals(
            [Range::fromString('08:30-12:30')],
            $range->break(Range::fromString('12:30-20:30'))
        );

        $range = Range::fromString('08:30-17:30');
        $this->assertEquals(
            [Range::fromString('08:30-17:30')],
            $range->break(Range::fromString('20:30-22:30'))
        );

        $range = Range::fromString('08:30-17:30');
        $this->assertEquals(
            [Range::fromString('10:30-17:30')],
            $range->break(Range::fromString('08:30-10:30'))
        );

        $range = Range::fromString('08:30-17:30');
        $this->assertEquals(
            [Range::fromString('08:30-12:00')],
            $range->break(Range::fromString('12:00-17:30'))
        );
    }
}
