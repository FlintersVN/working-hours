<?php

namespace Flinters\WorkingHours;

use Carbon\CarbonInterface;

class Time
{
    public $hour;

    public $minute;

    public function __construct($hour, $minute)
    {
        $this->hour = $hour;
        $this->minute = $minute;
    }

    public static function fromInt(int $time): Time
    {
        $parsed = static::parseFromInt($time);
        return new static($parsed[0], $parsed[1]);
    }

    public static function fromCarbon(CarbonInterface $carbon): Time
    {
        return new static($carbon->hour, $carbon->minute);
    }

    protected static function parseFromInt(int $time)
    {
        return [(int) floor($time / 60), $time % 60];
    }

    public static function fromString(string $time): Time
    {
        list($hour, $minute) = explode(':', $time);
        $hour = ltrim($hour, '0');
        $minute = ltrim($minute, '0');

        return new static((int) $hour, (int) $minute);
    }

    public function toInt()
    {
        return $this->hour * 60 + $this->minute;
    }

    public function toString(): string
    {
        $hour = str_pad($this->hour, '2', '0', STR_PAD_LEFT);
        $minute = str_pad($this->minute, '2', '0', STR_PAD_LEFT);
        return "$hour:$minute";
    }

    public function copy(): Time
    {
        return new static($this->hour, $this->minute);
    }

    public function gt(Time $time): bool
    {
        return $this->toInt() > $time->toInt();
    }

    public function gte(Time $time): bool
    {
        return $this->toInt() >= $time->toInt();
    }

    public function lt(Time $time): bool
    {
        return $this->toInt() < $time->toInt();
    }

    public function lte(Time $time): bool
    {
        return $this->toInt() <= $time->toInt();
    }

    public function eq(Time $time): bool
    {
        return $this->toInt() == $time->toInt();
    }

    public function min(Time $time): Time
    {
        return $this->lte($time) ? $this : $time;
    }

    public function max(Time $time): Time
    {
        return $this->gte($time) ? $this : $time;
    }

    public function addMinutes($minutes = 1): Time
    {
        $parsed = static::parseFromInt($this->toInt() + $minutes);
        $this->hour = $parsed[0];
        $this->minute = $parsed[1];
        return $this;
    }
}
