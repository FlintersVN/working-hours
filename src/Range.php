<?php

namespace Flinters\WorkingHours;

class Range
{
    /**
     * @var Time
     */
    public $start;
    /**
     * @var Time
     */
    public $end;

    public function __construct(Time $start, Time $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function contains(Time $time): bool
    {
        return $this->start->toInt() <= $time->toInt() && $time->toInt() <= $this->end->toInt();
    }

    public function containsRange(Range $range): bool
    {
        return $this->contains($range->start) || $this->contains($range->end);
    }

    public function merge(Range $range): Range
    {
        if ($this->contains($range->start) && $this->contains($range->end)) {
            return $this->copy();
        }

        if ($range->contains($this->start) && $range->contains($this->end)) {
            return $range->copy();
        }

        if ($this->containsRange($range) || $range->containsRange($this)) {
            return new static(
                $this->start->min($range->start),
                $this->end->max($range->end)
            );
        }
    }

    public function copy(): Range
    {
        return new static($this->start->copy(), $this->end->copy());
    }

    public static function duration(Time $start, int $duration): Range
    {
        return new static(
            $start->copy(),
            Time::fromInt($start->toInt() + $duration)
        );
    }

    public static function fromString($value): Range
    {
        list($start, $end) = explode('-', $value);

        return new static(
            Time::fromString($start),
            Time::fromString($end)
        );
    }

    public function copyStart(Time $to): Range
    {
        return new static(
            $this->start->copy(),
            $to->copy()
        );
    }

    public function copyEnd(Time $start): Range
    {
        return new static(
            $start->copy(),
            $this->end->copy()
        );
    }

    public function toString(): string
    {
        $start = $this->start->toString();
        $end = $this->end->toString();

        return "$start-$end";
    }

    public function addMinutes($minutes = 1): Range
    {
        $this->start->addMinutes($minutes);
        $this->end->addMinutes($minutes);
        return $this;
    }

    public function break(Range $break): array
    {
        // Range: 08:30-17:30
        // Break: 08:30-17:30,07:30-17:30,08:30-18:30
        if ($break->contains($this->start) && $break->contains($this->end)) {
            return [];
        }

        // Range: 08:30-17:30
        // Break: 08:30-12:30 ->12:30-17:30
        if ($break->start->lte($this->start) && $break->end->lt($this->end)) {
            return [
                $this->copyEnd($break->end)
            ];
        }

        // Range: 08:30-17:30
        // Break: 09:30-12:30 ->08:30-09:30,12:30-17:30
        if ($break->start->gt($this->start) && $break->end->lt($this->end)) {
            return [
                $this->copyStart($break->start),
                $this->copyEnd($break->end)
            ];
        }

        // Range: 08:30-17:30
        // Break: 12:30-17:30 -> 08:30-12:30
        if ($break->start->lt($this->end) && $break->end->gte($this->end)) {
            return [
                $this->copyStart($break->start)
            ];
        }

        // Otherwise, The break is out of range
        return [$this->copy()];
    }
}
