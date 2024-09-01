<?php

namespace App\Filament\Resources\BankReceiptResource\Pages;

use App\Filament\Resources\BankReceiptResource;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;

class ListBankReceipts extends ListRecords
{
    protected static string $resource = BankReceiptResource::class;
    public function getTitle(): string|Htmlable
    {
        return 'Bank Putri';
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Baru')
                ->color('info'),
            Action::make('editUser')
            ->label('Edit Nominal Awal')
            ->color('warning')
            ->icon('heroicon-m-document-arrow-down')
            ->form([
                TextInput::make('uang_awal')
                ->default(Auth::user()->uang_awal)
                ->label('Uang Awal?'),
                TextInput::make('phone')
                ->default(Auth::user()->phone)
                ->label('Nomer HP'),
            ])
            ->action(function (array $data) {
                $user = Auth::user();
                $user->uang_awal = $data['uang_awal'];
                $user->phone = $data['phone'];
                $user->update();
            }),
            Action::make('send-wa')
            ->label('Kirim Ke WhatsApp')
            ->color('success')
            ->icon('heroicon-o-chat-bubble-oval-left-ellipsis')
        // ->action(fn($data, $livewire) => dd()),
            ->url(function($data, $livewire){
                $datas = $livewire->getFilteredTableQuery()->get()->toArray();
                $sum = 0;
                foreach ($datas as $data) {
                    $sum += (int)$data['nominal'] + (int)$data['fee'];
                    $nama = $data['name'];
                    $bank = '(' . $data['bank'] . '-' . $data['account_number'] . ') = ';
                    $admin = 'Rp' . number_format($data['fee'] ?? 0, 2, ",", ".") . ' %2B Rp' . number_format((int)$data['nominal'], 2, ",", ".") ;
                    $nominal = $data['fee'] === null ? 'Rp' . number_format((int)$data['nominal'], 2, ",", ".") . "%0D%0A" : '= Rp' . number_format((int)$data['nominal'] + (int)$data['fee'], 2, ",", ".") . "%0D%0A";
                    $format = [
                        $nama,
                        $bank,
                        $data['fee'] === null ? '' : $admin,
                        $nominal ,
                    ];
                    $newData[] = implode(' ', $format);
                }

                if (isset($newData)) {
                    $uang_awal = Auth::user()->uang_awal;
                    $total = "Total : " . number_format($sum ?? 0, 2, ",", "."). "%0D%0A";
                    $uangAwal = "Uang Awal Bude: " . number_format($uang_awal ?? 0, 2, ",", ".") . "%0D%0A";
                    $sisaUang = "Sisa Uang: " . number_format((int) $uang_awal - (int) $sum, 2, ",", ".");
                    $msg = implode(' ', $newData);
                    return 'https://wa.me/' . Auth::user()->phone . '?text=' . $msg . $total . $uangAwal . $sisaUang;
                }
                return '#';
            }),
        ];
    }
    // public $defaultAction = 'onboarding';

    // public function onboardingAction(): Action
    // {
    //     return Action::make('onboarding')
    //     ->modalHeading('Welcome');
    // }
}
