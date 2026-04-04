<?php

namespace App\Livewire;

use App\Models\Category;
use App\Services\PdfImportService;
use App\Services\TransactionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Livewire\WithFileUploads;

class PdfImporter extends Component
{
    use WithFileUploads;

    public $file;

    public $step = 1; // 1: Upload, 2: Preview

    public $accountId;

    public $previewData = [];

    public function updatedFile()
    {
        $this->validate(['file' => 'required|mimes:pdf|max:5120']);
        $this->step = 2;
        $this->generatePreview();
    }

    public function generatePreview()
    {
        $path = $this->file->getRealPath();
        $pdfService = app(PdfImportService::class);

        $this->previewData = $pdfService->parseHdfcStatement($path, Auth::id())->toArray();
    }

    public function import()
    {
        $this->validate(['accountId' => 'required']);

        $executed = RateLimiter::attempt(
            'tx-import:' . Auth::id(),
            $maxAttempts = 60,
            function () {
                $txService = app(TransactionService::class);
                $count = 0;

                foreach ($this->previewData as $data) {
                    $data['user_id'] = Auth::id();
                    $data['account_id'] = $this->accountId;
                    $txService->create($data);
                    $count++;
                }

                session()->flash('success', "Successfully imported $count transactions from PDF.");

                return redirect()->route('transactions.index');
            }
        );

        if (!$executed) {
            session()->flash('error', 'Too many import requests. Please slow down.');
        }
    }

    public function render()
    {
        return view('livewire.pdf-importer', [
            'accounts' => Auth::user()->accounts,
            'categories' => Category::where('user_id', Auth::id())->orWhereNull('user_id')->get(),
        ])->layout('layouts.app');
    }
}
