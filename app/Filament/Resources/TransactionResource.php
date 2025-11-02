<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Asset;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
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
                        ->options(\App\Models\Asset::pluck('name', 'id')->toArray())
                        ->afterStateUpdated(fn($state, callable $set) => $set('currency_type', null)),

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
                    Select::make('currency_type')
                        ->label('Currency Type')
                        ->options([
                            'fiat' => 'Fiat',
                            'crypto' => 'Crypto',
                        ])
                        ->reactive()
                        ->visible(fn($get) => optional(Asset::find($get('asset_id')))->asset_type === 'crypto')
                        ->dehydrated(false),
                    Select::make('currency')
                        ->label('Currency')
                        ->options(function ($get) {
                            $asset = Asset::find($get('asset_id'));
                            $assetType = optional($asset)->asset_type;
                            $currencyType = $get('currency_type');
                            if ($assetType !== 'crypto' || $currencyType === 'fiat') {
                                return [
                                    'EUR' => 'EUR',
                                    'USD' => 'USD',
                                    'PLN' => 'PLN',
                                ];
                            }
                            if ($currencyType === 'crypto') {
                                return Asset::where('asset_type', 'crypto')->pluck('symbol', 'symbol');
                            }
                            return [];
                        })
                        ->visible(function ($get) {
                            $asset = Asset::find($get('asset_id'));
                            $assetType = optional($asset)->asset_type;
                            if ($assetType !== 'crypto') {
                                return true;
                            }
                            return filled($get('currency_type'));
                        }),
                ])->columns(2),
                Section::make('Transaction Details')->schema([
                    TextInput::make('quantity')
                        ->label('Quantity')
                        ->required()
                        ->numeric()
                        ->lazy()

                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $quantity = (float) $get('quantity');
                            $pricePerUnit = (float) $get('price_per_unit');
                            $totalFees = (float) $get('total_fees');
                            $type = $get('type');

                            $adjustedQuantity = $type === 'sell' ? -abs($quantity) : abs($quantity);
                            $set('total_value', $adjustedQuantity * $pricePerUnit - $totalFees);
                        }),

                    TextInput::make('price_per_unit')
                        ->label('Price per Unit')
                        ->required()
                        ->numeric()
                        ->lazy()

                        ->prefix(fn(callable $get) => strtoupper($get('currency')) ?: '')
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $quantity = (float) $get('quantity');
                            $pricePerUnit = (float) $get('price_per_unit');
                            $totalFees = (float) $get('total_fees');
                            $type = $get('type');

                            $adjustedQuantity = $type === 'sell' ? -abs($quantity) : abs($quantity);
                            $set('total_value', $adjustedQuantity * $pricePerUnit - $totalFees);
                        }),

                    /* TextInput::make('total_fees')
                        ->label('Total Fees')
                        ->required()
                        ->default(0)
                        ->numeric()
                        ->lazy()
                        
                        ->prefix(fn (callable $get) => strtoupper($get('currency')) ?: '')
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $quantity = (float) $get('quantity');
                            $pricePerUnit = (float) $get('price_per_unit');
                            $totalFees = (float) $get('total_fees');
                            $type = $get('type');

                            $adjustedQuantity = $type === 'sell' ? -abs($quantity) : abs($quantity);
                            $set('total_value', $adjustedQuantity * $pricePerUnit - $totalFees);
                        }), */

                    TextInput::make('total_value')
                        ->label('Total Value')
                        ->numeric()
                        ->reactive()
                        ->prefix(fn(callable $get) => strtoupper($get('currency')) ?: ''),

                    DatePicker::make('date')
                        ->required(),
                    Textarea::make('notes')->columnSpan(2)
                ])->columns(2),
                Section::make('Transaction Fees')
                    ->schema([
                        Repeater::make('fees')
                            ->relationship('fees')
                            ->label('Transaction Fees')
                            ->schema([
                                Select::make('type')
                                    ->label('Fee Type')
                                    ->options([
                                        'exchange' => 'Exchange fee',
                                        'network' => 'Network fee',
                                        'withdrawal' => 'Withdrawal fee',
                                    ])
                                    ->required(),
                                Select::make('currency_type')
                                    ->label('Currency Type')
                                    ->options([
                                        'fiat' => 'Fiat',
                                        'crypto' => 'Crypto',
                                    ])
                                    ->reactive(),
                                Select::make('currency')
                                    ->label('Currency')
                                    ->options(function ($get) {
                                        if ($get('currency_type') === 'fiat') {
                                            return [
                                                'EUR' => 'EUR',
                                                'USD' => 'USD',
                                                'PLN' => 'PLN',
                                            ];
                                        }
                                        if ($get('currency_type') === 'crypto') {
                                            return \App\Models\Asset::where('asset_type', 'crypto')
                                                ->pluck('symbol', 'symbol');
                                        }
                                        return [];
                                    })
                                    ->visible(fn($get) => filled($get('currency_type'))),
                                TextInput::make('value')
                                    ->numeric()
                                    ->required()
                                    ->prefix(fn(callable $get) => strtoupper($get('currency')) ?: '')
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->createItemButtonLabel('Add Fee')
                    ])
                    ->collapsible(),
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
                TextColumn::make('wallet.broker.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'buy' => 'success',
                        'sell' => 'danger',
                        'dividend' => 'primary',
                        'crypto_reward' => 'primary'
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'buy' => 'Buy',
                        'sell' => 'Sell',
                        'dividend' => 'Dividend',
                        'crypto_reward' => 'Crypto Reward'
                    }),
                TextColumn::make('reward_type')
                    ->label('Reward Type')
                    ->hidden(fn($record) => empty($record) || is_null($record->reward_type)),
                TextColumn::make('quantity')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $formatted = number_format($state, 8, '.', ' ');
                        return rtrim(rtrim($formatted, '0'), '.');
                    }),
                TextColumn::make('price_per_unit')
                    ->label('Price Per Unit')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $formatted = number_format($state, 8, '.', ' ');
                        return rtrim(rtrim($formatted, '0'), '.');
                    })
                    ->suffix(fn($record) => ' ' . ($record->currency ?? '')),
                TextColumn::make('total_fees')
                    ->label('Total Fees'),
                TextColumn::make('total_value')
                    ->label('Total Value')
                    ->sortable()
                    ->suffix(fn($record) => ' ' . strtoupper($record->currency ?? ''))
                    ->formatStateUsing(fn($state) => number_format($state, 2, '.', ' ')),

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
