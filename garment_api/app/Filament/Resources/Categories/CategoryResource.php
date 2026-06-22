<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\CategoryResource\Pages;
use App\Models\Category;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationLabel = 'Categories';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            \Filament\Forms\Components\TextInput::make('icon')
                ->maxLength(255)
                ->placeholder('e.g. shirt, jacket, dress')
                ->label('Icon Name'),
            \Filament\Forms\Components\Textarea::make('description')
                ->rows(3)
                ->label('Description')
                ->columnSpanFull(),
            \Filament\Forms\Components\Toggle::make('is_active')
                ->default(true)
                ->label('Active'),
            \Filament\Forms\Components\Hidden::make('user_id')
                ->default(fn() => auth()->id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(40)
                    ->default('—'),
                TextColumn::make('garments_count')
                    ->counts('garments')
                    ->label('Garments')
                    ->badge(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Created'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make()
                    ->url(fn (Category $record): string =>
                        static::getUrl('edit', ['record' => $record])),
                \Filament\Actions\DeleteAction::make()
                    ->action(fn (Category $record) => $record->delete()),
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
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }
}