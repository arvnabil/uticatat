<?php

namespace App\Filament\Resources\BankReceiptResource\Pages;

use App\Filament\Resources\BankReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditBankReceipt extends EditRecord
{
    protected static string $resource = BankReceiptResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    public function getTitle(): string|Htmlable
    {
        return 'Ubah Catatan Bank Putri';
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
