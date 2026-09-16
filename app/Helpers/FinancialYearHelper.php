<?php

namespace App\Helpers;

use Carbon\Carbon;

class FinancialYearHelper
{
    /**
     * Return the Indian financial year string for a given date.
     * FY = April 1 to March 31.
     *
     *   2025-04-01  → "2025-26"
     *   2025-12-31  → "2025-26"
     *   2026-01-15  → "2025-26"
     *   2026-03-31  → "2025-26"
     *   2026-04-01  → "2026-27"
     */
    public static function getFinancialYear($date = null): string
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();

        $year  = (int) $date->year;
        $month = (int) $date->month;

        $startYear = ($month >= 4) ? $year : ($year - 1);
        $endYear   = $startYear + 1;

        return $startYear . '-' . substr((string) $endYear, -2);
    }

    /**
     * Return start and end dates for a given FY string.
     *   "2025-26" → ['start' => '2025-04-01', 'end' => '2026-03-31']
     */
    public static function getFinancialYearRange(string $fy): array
    {
        [$startYear] = explode('-', $fy);
        $startYear = (int) $startYear;
        $endYear   = $startYear + 1;

        return [
            'start' => Carbon::create($startYear, 4, 1)->startOfDay()->toDateString(),
            'end'   => Carbon::create($endYear, 3, 31)->endOfDay()->toDateString(),
        ];
    }
}