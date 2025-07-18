<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\NumericColumn;
class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-on-square';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Transaction Context')->schema([
                    Select::make('wallet_id')
                        ->required()
                        ->label('Wallet')
                        ->relationship('wallet', 'name'),
                    Select::make('asset_id')
                        ->required()
                        ->label('Asset')
                        ->options(function () {
                            return \App\Models\Asset::with('exchange')->get()->mapWithKeys(function ($asset) {
                                return [
                                    $asset->id => $asset->name . ' / ' . ($asset->exchange->name),
                                ];
                            });
                        }),
                ])->columns(2),
                Section::make('Transaction Type')->schema([
                    Select::make('type')
                        ->required()
                        ->reactive()
                        ->options([
                            'buy' => 'Buy',
                            'sell' => 'Sell',
                            'dividend' => 'Dividend',
                            'crypto_reward' => 'Crypto reward'
                        ]),
                    Select::make('reward_type')
                        ->options([
                            'airdrop' => 'Airdrop',
                            'staking' => 'Staking',
                            'lauchpad' => 'Launchpad',
                            'launchpool' => 'Launchpool'
                        ])
                        ->visible(fn(callable $get) => $get('type') === 'crypto_reward'),
                ])->columns(2),
                Section::make('Transaction Details')->schema([
                    TextInput::make('currency')
                        ->required()
                        ->placeholder('PLN, USD, EUR')
                        ->reactive(),
                    TextInput::make('quantity')
                        ->label('Quantity')
                        ->required()
                        ->numeric()
                        ->inputMode('decimal')
                        ->placeholder(99.99999999)
                        ->minValue(0.00000001)
                        ->maxValue(99999999.99999999)
                        ->step(0.00000001),
                    TextInput::make('price_per_unit')
                        ->required()
                        ->numeric()
                        ->inputMode('decimal')
                        ->prefix(function (callable $get) {
                            $currency = strtoupper($get('currency'));
                            return $currency;
                        }),
                    TextInput::make('total_fees')
                        ->required()
                        ->default(0)
                        ->prefix(function (callable $get) {
                            $currency = strtoupper($get('currency'));
                            return $currency;
                        }),
                    DatePicker::make('date')
                        ->required(),
                    Textarea::make('notes')->columnSpan(2)
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('asset.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('wallet.user.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('wallet.exchange.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type'),
                TextColumn::make('reward_type')
                    ->label('Reward Type')
                    ->hidden(fn ($record) => empty($record) || is_null($record->reward_type)),
                TextColumn::make('quantity')
                    ->formatStateUsing(
                        fn($state) => (fmod($state, 1) === 0.0)
                            ? number_format($state, 0, ',', ' ')
                            : number_format($state, 8, ',', ' ')
                    ),
                TextColumn::make('price_per_unit')
                    ->label('Price Per Unit')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(function ($record) {
                        return ' ' .  $record->currency;
                    }),
                TextColumn::make('total_fees')
                    ->label('Total Fees')
                    ->numeric(decimalPlaces: 0)
                    ->suffix(function ($record) {
                        return ' ' .  $record->currency;
                    }),
                TextColumn::make('total_value')
                    ->label('Total Value')
                    ->getStateUsing(function ($record) {
                        return ($record->quantity * $record->price_per_unit) - $record->total_fees;
                    })
                    ->suffix(fn($record) => strtoupper($record->currency ?? ''))
                    ->formatStateUsing(fn($state) => number_format($state, 2))


            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                ])
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
