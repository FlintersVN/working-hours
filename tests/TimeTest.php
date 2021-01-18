<?php

namespace Flinters\OpenHour\Tests;

use Flinters\WorkingHours\Time;
use PHPUnit\Framework\TestCase;

class TimeTest extends TestCase
{
    public function test_from_string()
    {
        $time = Time::fromString('10:01');
        $this->assertEquals(10, $time->hour);
        $this->assertEquals(1, $time->minute);
        $this->assertEquals(601, $time->toInt());

        $time = Time::fromString('03:59');
        $this->assertEquals(3, $time->hour);
        $this->assertEquals(59, $time->minute);
        $this->assertEquals(239, $time->toInt());

        $time = Time::fromString('00:15');
        $this->assertEquals(0, $time->hour);
        $this->assertEquals(15, $time->minute);
        $this->assertEquals(15, $time->toInt());
    }

    public function test_from_int()
    {
        $time = Time::fromInt(601);
        $this->assertEquals(10, $time->hour);
        $this->assertEquals(1, $time->minute);

        $time = Time::fromInt(239);
        $this->assertEquals(3, $time->hour);
        $this->assertEquals(59, $time->minute);

        $time = Time::fromInt(240);
        $this->assertEquals(4, $time->hour);
        $this->assertEquals(0, $time->minute);
    }

    public function test_add()
    {
        $time = Time::fromString('08:30')->addMinutes(32);
        $this->assertEquals($time->hour, 9);
        $this->assertEquals($time->minute, 2);
    }

    public function test_to_string()
    {
        $time = new Time(3, 32);
        $this->assertEquals('03:32', $time->toString());

        $time = new Time(11, 5);
        $this->assertEquals('11:05', $time->toString());
    }
}
