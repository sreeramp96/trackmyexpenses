<?php

namespace App\Livewire;

use App\Models\Category;
use App\Services\CategorizationService;
use App\Services\ImportService;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class CsvImporter extends Component
{
    use WithFileUploads;

    public $file;

    public $step = 1; // 1: Upload, 2: Map, 3: Preview

    public $headers = [];

    public $rows = [];

    public $accountId;

    // Mapping
    public $mapDate;

    public $mapDescription;

    public $mapDebit;

    public $mapCredit;

    public $previewData = [];

    public function updatedFile()
    {
        $path = $this->file->getRealPath();
        $importService = app(ImportService::class);
        $this->rows = $importService->parseCsv($path)->toArray();
        $this->headers = array_keys($this->rows[0] ?? []);
        $this->step = 2;
    }

    public function generatePreview()
    {
        $this->validate([
            'accountId' => 'required',
            'mapDate' => 'required',
            'mapDescription' => 'required',
        ]);

        $importService = app(ImportService::class);
        $catService = app(CategorizationService::class);
        $this->previewData = [];

        foreach ($this->rows as $row) {
            $debit = $importService->parseCurrency($row[$this->mapDebit] ?? null);
            $credit = $importService->parseCurrency($row[$this->mapCredit] ?? null);

            $type = $credit > 0 ? 'income' : 'expense';
            $amount = $credit > 0 ? $credit : $debit;

            if ($amount <= 0) {
                continue;
            }

            $description = $row[$this->mapDescription];
            $suggestedCatId = $catService->suggestCategoryId($description, Auth::id());

            $this->previewData[] = [
                'transaction_date' => $importService->parseDate($row[$this->mapDate]),
                'note' => $description,
                'type' => $type,
                'amount' => $amount,
                'category_id' => $suggestedCatId,
                'account_id' => $this->accountId,
            ];
        }

        $this->step = 3;
    }

    public function import()
    {
        $txService = app(TransactionService::class);
        $count = 0;

        foreach ($this->previewData as $data) {
            $data['user_id'] = Auth::id();
            $txService->create($data);
            $count++;
        }

        session()->flash('success', "Successfully imported $count transactions.");

        return redirect()->route('transactions.index');
    }

    public function render()
    {
        return view('livewire.csv-importer', [
            'accounts' => Auth::user()->accounts,
            'categories' => Category::where('user_id', Auth::id())->orWhereNull('user_id')->get(),
        ])->layout('layouts.app');
    }
}
