<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Cuentas';

    protected static ?string $modelLabel = 'cuenta';

    protected static ?string $pluralModelLabel = 'cuentas';

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?int $navigationSort = 30;

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
                        Forms\Components\TextInput::make('amount')
                            ->label('Monto')
                            ->required()
                            ->autocomplete(false)
                            ->numeric(),
                        Forms\Components\Toggle::make('is_default')
                            ->label('Por defecto')
                            ->helperText('Se marcará por defecto esta cuenta')
                            ->inline(false)
                            ->default(false),
                        Forms\Components\Hidden::make('user_id')->default(auth()->id()),
                    ])
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Fecha de creación')
                            ->content(fn(Account $record): ?string => app_date($record->created_at)),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Última modificación')
                            ->content(fn(Account $record): ?string => app_date($record->updated_at)),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn(?Account $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->formatStateUsing(function ($record) {
                        return app_money($record->amount);
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('is_default')
                    ->label('Por defecto')
                    ->badge()
                    ->formatStateUsing(function ($record) {
                        return $record->is_default == '1' ? 'Si' : 'No';
                    })
                    ->color(static function ($state): string {
                        if ($state == '1') {
                            return 'success';
                        }
                        return 'info';
                    }),
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
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}
