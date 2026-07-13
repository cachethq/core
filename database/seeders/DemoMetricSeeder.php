<?php

namespace Cachet\Database\Seeders;

use Cachet\Models\Metric;
use DateTimeInterface;
use Illuminate\Database\Seeder;

class DemoMetricSeeder extends Seeder
{
    /**
     * The name of the metric created by the demo DatabaseSeeder.
     */
    public const METRIC_NAME = 'Cachet API Requests';

    /**
     * Push a fresh metric point onto the demo metric, if it still exists.
     */
    public function run(): void
    {
        $metric = Metric::query()->where('name', self::METRIC_NAME)->first();

        if ($metric === null) {
            return;
        }

        $metric->metricPoints()->create([
            'value' => self::valueAt(now()),
        ]);
    }

    /**
     * Calculate a realistic request rate for the given time: a daily traffic
     * curve peaking at 15:00 UTC, with a little random jitter on top.
     */
    public static function valueAt(DateTimeInterface $timestamp): float
    {
        $secondsIntoDay = ((int) $timestamp->format('H') * 3600)
            + ((int) $timestamp->format('i') * 60)
            + (int) $timestamp->format('s');

        $dailyCycle = cos(2 * M_PI * ($secondsIntoDay - (15 * 3600)) / 86400);

        $value = 45 + (30 * $dailyCycle) + (random_int(-80, 80) / 10);

        return round(max($value, 1), 2);
    }
}
