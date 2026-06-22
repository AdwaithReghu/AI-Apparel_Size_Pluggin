<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MerchantManagement\Pages;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;

class MerchantManagementResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Merchants';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Merchant';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Forms\Components\TextInput::make('name')
                ->required(),
            \Filament\Forms\Components\TextInput::make('email')
                ->email()
                ->required(),
            \Filament\Forms\Components\TextInput::make('password')
                ->password()
                ->required()
                ->label('Password')
                ->helperText('Default: merchant123'),
                
            \Filament\Forms\Components\TextInput::make('company')
                ->label('Company'),
            \Filament\Forms\Components\TextInput::make('phone')
                ->label('Phone'),
            \Filament\Forms\Components\TextInput::make('country')
                ->label('Country'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company')
                    ->default('—'),
                TextColumn::make('phone')
                    ->default('—'),
                TextColumn::make('country')
                    ->default('—'),
                TextColumn::make('garments_count')
                    ->counts('garments')
                    ->label('Garments')
                    ->badge(),
                TextColumn::make('api_calls_month')
                    ->label('API Calls')
                    ->default(0),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Joined'),
            ])
            ->actions([
                 \Filament\Actions\Action::make('suspend')
                    ->label('Suspend')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        // Suspend merchant by deleting their tokens
                        $record->tokens()->delete();
                    }),
                \Filament\Actions\EditAction::make()
                    ->url(fn (User $record): string =>
                        static::getUrl('edit', ['record' => $record])),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
{
    return [
        'index'  => Pages\ListMerchantManagement::route('/'),
        'create' => Pages\CreateMerchantManagement::route('/create'),
        'edit'   => Pages\EditMerchantManagement::route('/{record}/edit'),
    ];
}

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}