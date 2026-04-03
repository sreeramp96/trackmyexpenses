<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Smalot\PdfParser\Parser;

class PdfImportService
{
    /**
     * Extract transaction data from HDFC PDF statement.
     */
    public function parseHdfcStatement(string $path, int $userId): Collection
    {
        $parser = new Parser;
        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();

        $transactions = collect();
        $catService = app(CategorizationService::class);

        // Typical HDFC line: 01/03/26 DESCRIPTION 1,234.56 0.00 10,000.00
        // This regex is a simplified heuristic for HDFC-like layouts
        $lines = explode("\n", $text);

        foreach ($lines as $line) {
            // Match Date (dd/mm/yy) + Description + Withdrawal + Deposit
            // Regex: (\d{2}/\d{2}/\d{2})\s+(.+?)\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})
            if (preg_match('/(\d{2}\/\d{2}\/\d{2,4})\s+(.+?)\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})/', $line, $matches)) {
                $dateStr = $matches[1];
                $description = trim($matches[2]);
                $withdrawal = (float) str_replace(',', '', $matches[3]);
                $deposit = (float) str_replace(',', '', $matches[4]);

                if ($withdrawal == 0 && $deposit == 0) {
                    continue;
                }

                $type = $deposit > 0 ? 'income' : 'expense';
                $amount = $deposit > 0 ? $deposit : $withdrawal;

                $transactions->push([
                    'transaction_date' => $this->parseDate($dateStr),
                    'note' => $description,
                    'type' => $type,
                    'amount' => $amount,
                    'category_id' => $catService->suggestCategoryId($description, $userId),
                ]);
            }
        }

        return $transactions;
    }

    private function parseDate(string $date): string
    {
        try {
            return Carbon::createFromFormat('d/m/y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            } catch (\Exception $e2) {
                return now()->format('Y-m-d');
            }
        }
    }
}
