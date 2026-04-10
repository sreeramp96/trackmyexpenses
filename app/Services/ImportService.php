<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class ImportService
{
    /**
     * Parse CSV file and return rows as collection.
     */
    public function parseCsv(string $path): Collection
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            $headers = fgetcsv($handle, 1000, ',');
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $data = array_slice($data, 0, count($headers));
                $rows[] = array_combine($headers, $data);
            }
            fclose($handle);
        }

        return collect($rows);
    }

    /**
     * Clean and parse currency strings (e.g., "₹15,000.00" -> 15000.00).
     */
    public function parseCurrency(?string $value): float
    {
        if (empty($value)) {
            return 0.0;
        }

        // Remove symbols, commas and other non-numeric chars except dot
        $clean = preg_replace('/[^\d.]/', '', $value);

        return (float) $clean;
    }

    /**
     * Normalize dates from various formats to Y-m-d.
     */
    public function parseDate(string $date, string $format = 'm-d-Y'): string
    {
        try {
//            dd($date);
            return Carbon::createFromFormat($format, $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return now()->format('Y-m-d');
        }
    }
}
