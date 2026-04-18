<?php

namespace App\Filament\Resources\Transactions\Pages;

use App\Filament\Resources\TemporaryTransactions\TemporaryTransactionResource;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Account;
use App\Models\TemporaryTransaction;
use App\Services\CategorizationService;
use App\Services\ImportService;
use App\Services\PdfImportService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImportTransactions extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = TransactionResource::class;

    protected string $view = 'filament.pages.import-transactions';

    public ?array $data = [];

    public int $step = 1;

    public array $headers = [];

    public array $rows = [];

    public bool $isPdf = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file')
                    ->label('Bank Statement')
                    ->acceptedFileTypes([
                        'text/csv',
                        'text/plain',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/pdf',
                    ])
                    ->maxSize(5120)
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => $this->handleUpload($state))
                    ->visible(fn () => $this->step === 1),

                Select::make('account_id')
                    ->label('Target Account')
                    ->options(Account::where('user_id', Auth::id())->pluck('name', 'id'))
                    ->required()
                    ->native(false)
                    ->visible(fn () => $this->step === 2),

                Select::make('map_date')
                    ->label('Date Column')
                    ->options(fn () => array_combine($this->headers, $this->headers))
                    ->required()
                    ->native(false)
                    ->hidden(fn () => $this->isPdf)
                    ->visible(fn () => $this->step === 2),

                Select::make('map_description')
                    ->label('Description Column')
                    ->options(fn () => array_combine($this->headers, $this->headers))
                    ->required()
                    ->native(false)
                    ->hidden(fn () => $this->isPdf)
                    ->visible(fn () => $this->step === 2),

                Select::make('map_debit')
                    ->label('Debit / Withdrawal Column')
                    ->options(fn () => array_combine($this->headers, $this->headers))
                    ->native(false)
                    ->hidden(fn () => $this->isPdf)
                    ->visible(fn () => $this->step === 2),

                Select::make('map_credit')
                    ->label('Credit / Deposit Column')
                    ->native(false)
                    ->options(fn () => array_combine($this->headers, $this->headers))
                    ->hidden(fn () => $this->isPdf)
                    ->visible(fn () => $this->step === 2),
            ])
            ->statePath('data');
    }

    public function handleUpload($file)
    {
        if (! $file) {
            return;
        }

        $uploadedFile = is_array($file) ? array_values($file)[0] : $file;
        if (! ($uploadedFile instanceof TemporaryUploadedFile)) {
            return;
        }

        $path = $uploadedFile->getRealPath();
        $extension = $uploadedFile->getClientOriginalExtension();

        if (strtolower($extension) === 'pdf') {
            $this->isPdf = true;
            $pdfService = app(PdfImportService::class);
            $preview = $pdfService->parseHdfcStatement($path, Auth::id())->toArray();

            if (count($preview) > 0) {
                $this->rows = $preview;
                $this->step = 2;
            } else {
                Notification::make()
                    ->title('Could not find any transaction data in the PDF')
                    ->danger()
                    ->send();
            }

            return;
        }

        $this->isPdf = false;
        $importService = app(ImportService::class);

        if (in_array(strtolower($extension), ['xls', 'xlsx'])) {
            $this->rows = $importService->parseExcel($path)->toArray();
        } else {
            $this->rows = $importService->parseCsv($path)->toArray();
        }

        if (count($this->rows) > 0) {
            $this->headers = array_keys($this->rows[0]);
            $this->step = 2;
        } else {
            Notification::make()
                ->title('Could not find any data in the file')
                ->danger()
                ->send();
        }
    }

    public function stageForReview()
    {
        $formData = $this->form->getState();
        $importService = app(ImportService::class);
        $catService = app(CategorizationService::class);

        // Clear existing staging data for this user
        TemporaryTransaction::where('user_id', Auth::id())->delete();

        $count = 0;

        foreach ($this->rows as $row) {
            if ($this->isPdf) {
                // PDF already parsed in handleUpload
                $data = $row;
                $data['account_id'] = $formData['account_id'];
                $data['user_id'] = Auth::id();
            } else {
                $debit = $importService->parseCurrency($row[$formData['map_debit']] ?? 0);
                $credit = $importService->parseCurrency($row[$formData['map_credit']] ?? 0);

                $type = $credit > 0 ? 'income' : 'expense';
                $amount = $credit > 0 ? $credit : $debit;

                if ($amount <= 0) {
                    continue;
                }

                $description = $row[$formData['map_description']] ?? 'No description';
                $suggestedCatId = $catService->suggestCategoryId($description, Auth::id());

                $data = [
                    'user_id' => Auth::id(),
                    'account_id' => $formData['account_id'],
                    'transaction_date' => $importService->parseDate($row[$formData['map_date']] ?? null),
                    'note' => $description,
                    'type' => $type,
                    'amount' => $amount,
                    'category_id' => $suggestedCatId,
                ];
            }

            TemporaryTransaction::create($data);
            $count++;
        }

        Notification::make()
            ->title("Staged $count transactions for review.")
            ->success()
            ->send();

        return redirect()->to(TemporaryTransactionResource::getUrl());
    }
}
