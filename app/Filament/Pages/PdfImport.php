<?php

namespace App\Filament\Pages;

use App\Models\Account;
use App\Models\Category;
use App\Services\PdfImportService;
use App\Services\TransactionService;
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

class PdfImport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.pdf-import';
    
    protected static string|UnitEnum|null $navigationGroup = 'Utilities';

    protected static ?string $title = 'PDF Statement Import';

    public ?array $data = [];
    public int $step = 1;
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
                    ->label('PDF Statement (HDFC)')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(5120)
                    ->reactive()
                    ->afterStateUpdated(fn ($state) => $this->handleUpload($state))
                    ->visible(fn () => $this->step === 1),

                Select::make('account_id')
                    ->label('Target Account')
                    ->options(Account::where('user_id', Auth::id())->pluck('name', 'id'))
                    ->required()
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
        $pdfService = app(PdfImportService::class);
        $this->previewData = $pdfService->parseHdfcStatement($path, Auth::id())->toArray();

        if (count($this->previewData) > 0) {
            $this->step = 2;
        }
    }

    public function removeItem($index)
    {
        unset($this->previewData[$index]);
        $this->previewData = array_values($this->previewData);
        if (empty($this->previewData)) $this->step = 1;
    }

    public function import()
    {
        $formData = $this->form->getState();
        
        $executed = RateLimiter::attempt(
            'tx-import:' . Auth::id(),
            60,
            function () use ($formData) {
                $txService = app(TransactionService::class);
                $count = 0;

                foreach ($this->previewData as $data) {
                    $data['user_id'] = Auth::id();
                    $data['account_id'] = $formData['account_id'];
                    $txService->create($data);
                    $count++;
                }

                Notification::make()
                    ->title("Successfully imported $count transactions from PDF")
                    ->success()
                    ->send();

                return redirect()->to('/admin/transactions');
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
