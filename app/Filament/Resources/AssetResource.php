<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Filament\Resources\AssetResource\RelationManagers;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Asset')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('symbol')
                            ->required()
                            ->maxLength(255)
                            ->extraAttributes(['style' => 'text-transform: uppercase;'])
                            ->dehydrateStateUsing(fn ($state) => strtoupper($state)),
                        Select::make('asset_type')
                            ->required()
                            ->label('Asset type')
                            ->options([
                                'stock' => 'Stock',
                                'etf' => 'ETF',
                                'cfd' => 'CFD',
                                'crypto' => 'Crypto',
                                'bond' => 'Bond',
                                'forex' => 'Forex',
                            ]),
                        Select::make('broker_id')
                            ->required()
                            ->label('Broker/Crypto exchange')
                            ->relationship('brokers', 'name'),
                        Select::make('exchange_id')
                            ->required()
                            ->label('Exchange')
                            ->relationship('exchange', 'symbol'),
                        FileUpload::make('image')
                            ->image()
                            ->directory('assets')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                ImageColumn::make('image'),
                TextColumn::make('brokers.name'),
                TextColumn::make('symbol'),
                TextColumn::make('asset_type')
                    ->label('Asset Type')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'stock' => 'Stock',
                        'etf' => 'ETF',
                        'cfd' => 'CFD',
                        'crypto' => 'Crypto',
                        'bond' => 'Bond',
                        'forex' => 'Forex',
                        default => $state
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
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
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
