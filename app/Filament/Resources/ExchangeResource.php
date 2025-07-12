<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExchangeResource\Pages;
use App\Filament\Resources\ExchangeResource\RelationManagers;
use App\Models\Exchange;
use Filament\Actions\SelectAction;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\SelectAction as ActionsSelectAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExchangeResource extends Resource
{
    protected static ?string $model = Exchange::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
    ->schema([
        Group::make()
            ->schema([
                Section::make('Exchange information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Select::make('type')
                            ->options([
                                'broker' => 'Broker',
                                'crypto_exchange' => 'Crypto exchange'
                            ])
                            ->required(),
                        TextInput::make('url')
                            ->required()
                            ->columnSpanFull(),
                        FileUpload::make('image')
                            ->image()
                            ->directory('exchanges')
                            ->imagePreviewHeight('200')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->columnSpan(2),
        Section::make('Optional')
            ->schema([
                TextInput::make('country'),
                TextInput::make('currency'),
            ])
            ->columnSpan(1),
    ])
    ->columns(3);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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
            'index' => Pages\ListExchanges::route('/'),
            'create' => Pages\CreateExchange::route('/create'),
            'edit' => Pages\EditExchange::route('/{record}/edit'),
        ];
    }
}
