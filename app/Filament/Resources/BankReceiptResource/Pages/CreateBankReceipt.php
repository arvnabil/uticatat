<?php

namespace App\Filament\Resources\BankReceiptResource\Pages;

use App\Filament\Resources\BankReceiptResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateBankReceipt extends CreateRecord
{
    protected static string $resource = BankReceiptResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getTitle(): string|Htmlable
    {
        return 'Tambah Catatan Bank Putri';
    }
}
