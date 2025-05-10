<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Categorías';

    protected static ?string $modelLabel = 'categoría';

    protected static ?string $pluralModelLabel = 'categorías';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->autocomplete(false)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->label('Descripción')
                            ->autocomplete(false)
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_default')
                            ->label('Por defecto')
                            ->helperText('Se marcará por defecto esta categoría')
                            ->inline(false)
                            ->default(false),
                    ])
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Fecha de creación')
                            ->content(fn(Category $record): ?string => app_date($record->created_at)),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Última modificación')
                            ->content(fn(Category $record): ?string => app_date($record->updated_at)),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn(?Category $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_default')
                    ->label('Por defecto')
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        return $record->is_default == '1' ? 'Si' : 'No';
                    })->color(static function ($state): string {
                        if ($state == '1') {
                            return 'success';
                        }
                        return 'info';
                    })
                    ,
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha creación')
                    ->dateTime(app_datetime())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Fecha modificación')
                    ->dateTime(app_datetime())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
