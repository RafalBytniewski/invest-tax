<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExchangeResource\Pages;
use App\Filament\Resources\ExchangeResource\RelationManagers;
use App\Models\Exchange;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use function PHPSTORM_META\map;

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
                                Group::make()
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('symbol')                                         
                                            ->maxLength(25),
                                    ])->columns(2),
                                Group::make()
                                    ->schema([
                                        Select::make('country')
                                            ->options([
                                                'poland' => 'Poland',
                                                'usa' => 'USA',
                                                'germany' => 'Germany',
                                                'great_britain' => 'Great Britain',
                                            ])
                                            ->required(),
                                        Select::make('currency')
                                            ->options([
                                                'PLN', 'USD','EUR','GBP'
                                            ])
                                            ->required(),
                                    ])->columns(2),
                                Group::make()
                                    ->schema([
                                        TextInput::make('timezone')
                                            ->required(),
                                        TextInput::make('trading_hours')
                                            ->required(),
                                    ])->columns(2),
                                TextInput::make('url')
                                    ->required()
                                    ->url(),
                                FileUpload::make('image')
                                    ->image()
                                    ->columnSpanFull(),
                            ]),
                    ])
            ])->columns(1);
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
