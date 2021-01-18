<?php

namespace Flinters\WorkingHours;

use Carbon\CarbonInterface;

final class WorkingHours
{
    protected $free = [];

    public function __construct(Range $range = null)
    {
        if ($range) {
            $this->free = [$range];
        }
    }

    public static function dayOff(): WorkingHours
    {
        return new self();
    }

    public static function working(Range $range): WorkingHours
    {
        return new self($range);
    }

    public function isFree(Range $range): bool
    {
        foreach ($this->free as $free) {
            if ($free->contains($range->start) && $free->contains($range->end)) {
                return true;
            }
        }

        return false;
    }

    public function nextAvailable(Carbon $time, $duration, $waitDuration, Carbon $waitingFrom = null): ?Carbon
    {
        $nextAvailable = Time::fromCarbon($time);
        $maxWaitingTime = $waitingFrom
            ? Time::fromCarbon($waitingFrom->copy()->addMinutes($waitDuration))
            : Time::fromCarbon($time->copy()->addMinutes($waitDuration));

        $range = Range::duration($nextAvailable, $duration);
        while ($range->start->lte($maxWaitingTime)) {
            if ($this->isFree($range)) {
                return $time->copy()->setHour($range->start->hour)->setMinutes($range->start->minute);
            }

            $range->addMinutes();
        }

        return null;
    }

    public function head(): Range
    {
        return $this->free[0];
    }

    public function tail(): Range
    {
        return $this->free[count($this->free) - 1];
    }

    public function busy(Range $busy): WorkingHours
    {
        if (! $this->free) {
            return $this;
        }

        if ($this->head()->start->gte($busy->start) && $this->tail()->end->lte($busy->end)) {
            $this->free = [];
            return $this;
        }

        if ($this->head()->start->gte($busy->end)) {
            return $this;
        }

        if ($this->tail()->end->lte($busy->start)) {
            return $this;
        }

        $free = [];
        foreach ($this->free as $range) {
            if ($range->containsRange($busy)) {
                $ranges = $range->break($busy);
                $free = array_merge($free, $ranges);
            } else {
                $free[] = $range;
            }
        }

        $this->free = $free;

        return $this;
    }

    public function busyDuration(Time $from, $duration): WorkingHours
    {
        return $this->busy(Range::duration($from, $duration));
    }

    public function sortFreeTimes()
    {
        usort($this->free, function (Range $range) {
            return $range->start->toInt();
        });
    }

    public function ranges(): array
    {
        return $this->free;
    }

    public function copy(): WorkingHours
    {
        $instance = new self;
        $instance->free = $this->free;
        return $instance;
    }
}
