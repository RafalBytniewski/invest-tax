<?php

namespace App\Filament\Resources;

use App\Filament\Resources\brokerResource\Pages;
use App\Filament\Resources\brokerResource\RelationManagers;
use App\Models\broker;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
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
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class brokerResource extends Resource
{
    protected static ?string $model = broker::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Broker information')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('type')
                                    ->options([
                                        'broker' => 'Broker',
                                        'crypto_broker' => 'Crypto broker'
                                    ])
                                    ->required(),
                                TextInput::make('url')
                                    ->required()
                                    ->url()
                                    ->columnSpanFull(),
                                FileUpload::make('image')
                                    ->image()
                                    ->directory('brokers')
                                    ->imagePreviewHeight('200')
                                    ->visibility('public')
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
                TextColumn::make('name'),
                ImageColumn::make('image'),
                TextColumn::make('type')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'broker' => 'Broker',
                        'crypto_broker' => 'Crypto broker',
                        default => $state,
                    }),
                TextColumn::make('url')
                    ->copyable(),
                TextColumn::make('country'),
                TextColumn::make('currency'),
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
            'index' => Pages\Listbrokers::route('/'),
            'create' => Pages\Createbroker::route('/create'),
            'edit' => Pages\Editbroker::route('/{record}/edit'),
        ];
    }
}
