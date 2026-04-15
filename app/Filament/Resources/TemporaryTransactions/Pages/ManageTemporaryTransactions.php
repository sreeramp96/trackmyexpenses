<?php

namespace App\Filament\Resources\TemporaryTransactions\Pages;

use App\Filament\Resources\TemporaryTransactions\TemporaryTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTemporaryTransactions extends ManageRecords
{
    protected static string $resource = TemporaryTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
