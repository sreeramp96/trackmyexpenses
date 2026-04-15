<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
            if ($headers) {
                $headers = array_map('trim', $headers);
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    if (count($headers) === count($data)) {
                        $rows[] = array_combine($headers, $data);
                    }
                }
            }
            fclose($handle);
        }

        return collect($rows);
    }

    /**
     * Parse Excel file (XLS or XLSX) and return rows as collection.
     */
    public function parseExcel(string $path): Collection
    {
        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if (empty($rows)) {
            return collect();
        }

        // Improved Header Detection: Search for row containing essential keywords
        $headerRowIndex = 0;
        $found = false;
        $keywords = ['date', 'description', 'narration', 'particulars', 'amount', 'debit', 'txn date'];

        foreach ($rows as $index => $row) {
            $rowString = strtolower(implode(' ', array_filter($row)));

            $matchCount = 0;
            foreach ($keywords as $word) {
                if (str_contains($rowString, $word)) {
                    $matchCount++;
                }
            }

            // If we find at least 2 financial keywords, it's almost certainly the header
            if ($matchCount >= 2) {
                $headerRowIndex = $index;
                $found = true;
                break;
            }
        }

        // If no header found, fallback to first non-empty row
        if (! $found) {
            foreach ($rows as $index => $row) {
                if (count(array_filter($row)) > 3) {
                    $headerRowIndex = $index;
                    break;
                }
            }
        }

        $headers = $rows[$headerRowIndex];
        $cleanHeaders = [];
        foreach ($headers as $i => $h) {
            $cleanHeaders[$i] = ! empty($h) ? trim($h) : 'Column_'.($i + 1);
        }

        $dataRows = array_slice($rows, $headerRowIndex + 1);

        $formattedRows = [];
        foreach ($dataRows as $row) {
            $filtered = array_filter($row);
            if (count($filtered) > 0) {
                $rowString = strtolower(implode(' ', $filtered));
                // Skip footer/summary rows
                if (! str_contains($rowString, 'statement summary') && ! str_contains($rowString, 'total amount') && ! str_contains($rowString, 'generated on')) {
                    $rowValues = array_slice($row, 0, count($cleanHeaders));
                    if (count($rowValues) < count($cleanHeaders)) {
                        $rowValues = array_pad($rowValues, count($cleanHeaders), null);
                    }
                    $formattedRows[] = array_combine($cleanHeaders, $rowValues);
                }
            }
        }

        return collect($formattedRows);
    }

    /**
     * Clean and parse currency strings (e.g., "₹15,000.00" -> 15000.00).
     */
    public function parseCurrency($value): float
    {
        if (is_null($value) || $value === '' || $value === 0 || $value === '0') {
            return 0.0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        // Remove symbols, commas and other non-numeric chars except dot
        $clean = preg_replace('/[^\d.]/', '', (string) $value);

        return (float) $clean;
    }

    /**
     * Normalize dates from various formats to Y-m-d.
     */
    public function parseDate($date, string $format = 'd/m/Y'): string
    {
        if (empty($date)) {
            return now()->format('Y-m-d');
        }

        // If it's a numeric date from Excel
        if (is_numeric($date)) {
            try {
                return Date::excelToDateTimeObject($date)->format('Y-m-d');
            } catch (\Exception $e) {
            }
        }

        try {
            if (str_contains($date, '-')) {
                return Carbon::parse($date)->format('Y-m-d');
            }
            if (preg_match('/[a-zA-Z]/', $date)) {
                return Carbon::parse($date)->format('Y-m-d');
            }

            return Carbon::createFromFormat($format, $date)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $ex) {
                return now()->format('Y-m-d');
            }
        }
    }
}
