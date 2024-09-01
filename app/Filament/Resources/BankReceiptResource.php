<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankReceiptResource\Pages;
use App\Filament\Resources\BankReceiptResource\RelationManagers;
use App\Models\BankReceipt;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BankReceiptResource extends Resource
{
    protected static ?string $model = BankReceipt::class;
    protected static ?string $navigationGroup = 'Catatan Putri';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    public static function getNavigationLabel(): string
    {
        return __('Catatan Bank Putri');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                ->required()
                ->label('Nama Pengirim'),
                DatePicker::make('tanggal')
                ->label('Tanggal Transfer'),
                TextInput::make('bank')
                ->label('Bank apa?'),
                TextInput::make('account_number')
                ->label('Nomer Bank'),
                TextInput::make('nominal')
                ->label('Nominal')
                ->required(),
                TextInput::make('fee')
                ->label('Biaya Admin (optional)'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->label('Nama Pengirim'),
                TextColumn::make('tanggal')
                ->label('Tanggal Transfer'),
                TextColumn::make('bank')
                ->label('Kode Bank'),
                TextColumn::make('account_number')
                ->label('Nomer Bank'),
                TextColumn::make('nominal')
                ->label('Jumlah'),
                TextColumn::make('fee')
                ->label('Biaya Admin'),
            ])
            ->filters([
            Filter::make('tanggal')
            ->form([
                DatePicker::make('dari_tanggal'),
                DatePicker::make('sampai_tanggal'),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                ->when(
                    $data['dari_tanggal'],
                    fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                )
                    ->when(
                        $data['sampai_tanggal'],
                        fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                    );
            })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBankReceipts::route('/'),
            'create' => Pages\CreateBankReceipt::route('/create'),
            'edit' => Pages\EditBankReceipt::route('/{record}/edit'),
        ];
    }
}
