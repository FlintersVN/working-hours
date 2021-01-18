<?php

namespace Flinters\OpenHour\Tests;

use Carbon\Carbon;
use Flinters\WorkingHours\Range;
use Flinters\WorkingHours\Time;
use Flinters\WorkingHours\WorkingHours;
use PHPUnit\Framework\TestCase;

class WorkingHoursTest extends TestCase
{
    public function test_busy()
    {
        $workingHours = new WorkingHours(Range::fromString('08:30-17:30'));
        $workingHours->busy(Range::fromString('07:30-09:30'));
        $this->assertEquals(
            [Range::fromString('09:30-17:30')],
            $workingHours->ranges()
        );

        $workingHours = new WorkingHours(Range::fromString('08:30-17:30'));
        $workingHours->busy(Range::fromString('07:30-09:30'));
        $workingHours->busy(Range::fromString('10:30-12:30'));
        $this->assertEquals(
            [Range::fromString('09:30-10:30'), Range::fromString('12:30-17:30')],
            $workingHours->ranges()
        );

        $workingHours = new WorkingHours(Range::fromString('08:30-17:30'));
        $workingHours->busy(Range::fromString('07:30-08:29'));
        $this->assertEquals(
            [Range::fromString('08:30-17:30')],
            $workingHours->ranges()
        );

        $workingHours = new WorkingHours(Range::fromString('08:30-17:30'));
        $workingHours->busy(Range::fromString('07:30-20:30'));
        $this->assertEquals(
            [],
            $workingHours->ranges()
        );

        $workingHours = new WorkingHours(Range::fromString('08:30-17:30'));
        $workingHours->busy(Range::duration(Time::fromString('08:30'), 90));
        $workingHours->busy(Range::fromString('10:30-12:00'));
        $workingHours->busy(Range::fromString('12:30-14:00'));
        $this->assertEquals(
            [Range::fromString('10:00-10:30'), Range::fromString('12:00-12:30'), Range::fromString('14:00-17:30')],
            $workingHours->ranges()
        );

        $workingHours = new WorkingHours(Range::fromString('08:30-17:30'));
        $workingHours->busyDuration(Time::fromString('08:30'), 90);
        $this->assertEquals(
            [Range::fromString('10:00-17:30')],
            $workingHours->ranges()
        );
    }

    public function test_is_free()
    {
        $workingHours = new WorkingHours(Range::fromString('08:30-17:30'));
        $this->assertTrue($workingHours->isFree(Range::fromString('10:00-12:00')));

        $workingHours = new WorkingHours(Range::fromString('08:30-17:30'));
        $workingHours->busy(Range::fromString('08:30-12:00'));
        $this->assertTrue($workingHours->isFree(Range::fromString('12:00-13:00')));
        $this->assertFalse($workingHours->isFree(Range::fromString('08:30-10:00')));

        $dayOff = WorkingHours::dayOff();
        $this->assertFalse($dayOff->isFree(Range::fromString('08:30-10:00')));
    }

    public function test_first_available()
    {
        $day = WorkingHours::working(Range::fromString('00:00-23:59'));
        $day->busy(Range::fromString('08:15-08:30'));
        $this->assertEquals(Carbon::parse('08:30'), $day->nextAvailable(Carbon::parse('08:15'), 20, 20));
        $this->assertEquals(Carbon::parse('08:35'), $day->nextAvailable(Carbon::parse('08:35'), 20, 20));
        $this->assertEquals(null, $day->nextAvailable(Carbon::parse('08:00'), 20, 20));
    }
}
