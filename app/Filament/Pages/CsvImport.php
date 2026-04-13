<?php

namespace App\Filament\Pages;

use App\Models\Account;
use App\Models\Category;
use App\Services\CategorizationService;
use App\Services\ImportService;
use App\Services\TransactionService;
use App\Filament\Resources\Transactions\TransactionResource;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

class CsvImport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-arrow-up';

    protected string $view = 'filament.pages.csv-import';

    protected static string|UnitEnum|null $navigationGroup = 'Utilities';

    protected static ?string $title = 'Statement Import';

    public ?array $data = [];
    public int $step = 1;
    public array $headers = [];
    public array $rows = [];
    public array $previewData = [];
    public array $categories = [];

    public function mount(): void
    {
        $this->form->fill();
        $this->categories = Category::where('user_id', Auth::id())
            ->orWhereNull('user_id')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file')
                    ->label('Bank Statement (CSV, XLS, XLSX)')
                    ->acceptedFileTypes([
                        'text/csv',
                        'text/plain',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    ])
                    ->maxSize(5120)
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => $this->handleUpload($state))
                    ->visible(fn () => $this->step === 1),

                Select::make('account_id')
                    ->label('Target Account')
                    ->options(Account::where('user_id', Auth::id())->pluck('name', 'id'))
                    ->required()
                    ->visible(fn () => $this->step === 2),

                Select::make('map_date')
                    ->label('Date Column')
                    ->options(fn() => array_combine($this->headers, $this->headers))
                    ->required()
                    ->visible(fn () => $this->step === 2),

                Select::make('map_description')
                    ->label('Description Column')
                    ->options(fn() => array_combine($this->headers, $this->headers))
                    ->required()
                    ->visible(fn () => $this->step === 2),

                Select::make('map_debit')
                    ->label('Debit / Withdrawal Column')
                    ->options(fn() => array_combine($this->headers, $this->headers))
                    ->visible(fn () => $this->step === 2),

                Select::make('map_credit')
                    ->label('Credit / Deposit Column')
                    ->options(fn() => array_combine($this->headers, $this->headers))
                    ->visible(fn () => $this->step === 2),
            ])
            ->statePath('data');
    }

    public function handleUpload($file)
    {
        if (!$file) return;

        $uploadedFile = is_array($file) ? array_values($file)[0] : $file;
        if (!($uploadedFile instanceof TemporaryUploadedFile)) return;

        $path = $uploadedFile->getRealPath();
        $extension = $uploadedFile->getClientOriginalExtension();

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

    public function generatePreview()
    {
        $formData = $this->form->getState();
        $importService = app(ImportService::class);
        $catService = app(CategorizationService::class);

        $this->previewData = [];

        foreach ($this->rows as $index => $row) {
            $debit = $importService->parseCurrency($row[$formData['map_debit']] ?? 0);
            $credit = $importService->parseCurrency($row[$formData['map_credit']] ?? 0);

            $type = $credit > 0 ? 'income' : 'expense';
            $amount = $credit > 0 ? $credit : $debit;

            if ($amount <= 0) continue;

            $description = $row[$formData['map_description']] ?? 'No description';
            $suggestedCatId = $catService->suggestCategoryId($description, Auth::id());

            $this->previewData[$index] = [
                'transaction_date' => $importService->parseDate($row[$formData['map_date']] ?? null),
                'note' => $description,
                'type' => $type,
                'amount' => $amount,
                'category_id' => $suggestedCatId,
                'account_id' => $formData['account_id'],
            ];
        }

        $this->step = 3;
    }

    public function removeItem($index)
    {
        unset($this->previewData[$index]);
        $this->previewData = array_values($this->previewData);
        if (empty($this->previewData)) $this->step = 2;
    }

    public function import()
    {
        $executed = RateLimiter::attempt(
            'tx-import:' . Auth::id(),
            60,
            function () {
                $txService = app(TransactionService::class);
                $count = 0;

                foreach ($this->previewData as $data) {
                    $data['user_id'] = Auth::id();
                    $txService->create($data);
                    $count++;
                }

                Notification::make()
                    ->title("Successfully imported $count transactions")
                    ->success()
                    ->send();

                return redirect()->to(TransactionResource::getUrl());
            }
        );

        if (!$executed) {
            Notification::make()
                ->title('Too many import requests')
                ->danger()
                ->send();
        }
    }
}
