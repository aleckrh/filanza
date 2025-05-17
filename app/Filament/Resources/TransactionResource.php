<?php

namespace App\Filament\Resources;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Transacciones';

    protected static ?string $modelLabel = 'transaccion';

    protected static ?string $pluralModelLabel = 'transacciones';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        $category = Category::where('is_default', true)->first();
        $account = Account::where('is_default', true)->where('user_id', auth()->id())->first();

        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Select::make('account_id')
                                    ->label('Cuenta')
                                    ->relationship(
                                        name: 'account',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query) => $query->where('user_id', '=', auth()->id()),
                                    )
                                    ->default($account->id ?? null),
                                Forms\Components\Select::make('category_id')
                                    ->label('Categoría')
                                    ->relationship('category', 'name')
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nombre')
                                            ->required()
                                            ->autocomplete(false)
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('description')
                                            ->label('Descripción')
                                            ->autocomplete(false)
                                            ->maxLength(255),
                                    ])
                                    ->default($category->id ?? null),
                            ])->columns(),
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->autocomplete(false)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->label('Descripción')
                            ->autocomplete(false)
                            ->maxLength(255),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Tipo')
                                    ->required()
                                    ->options(TransactionTypeEnum::labels())
                                    ->default(TransactionTypeEnum::EXPENSE->value),
                                Forms\Components\TextInput::make('amount')
                                    ->label('Monto')
                                    ->required()
                                    ->autocomplete(false)
                                    ->numeric(),
                                Forms\Components\DateTimePicker::make('date_time')
                                    ->label('Fecha y hora')
                                    ->required()
                                    ->default(now()),
                            ])->columns(3),
                    ])
                    ->columnSpan(['lg' => 2]),
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Placeholder::make('created_at')
                            ->label('Fecha de creación')
                            ->content(fn(Transaction $record): ?string => app_date($record->created_at)),

                        Forms\Components\Placeholder::make('updated_at')
                            ->label('Última modificación')
                            ->content(fn(Transaction $record): ?string => app_date($record->updated_at)),
                    ])
                    ->columnSpan(['lg' => 1])
                    ->hidden(fn(?Transaction $record) => $record === null),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account.name')
                    ->label('Cuenta'),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoría'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Monto')
                    ->color(function ($record) {
                        return $record->type == TransactionTypeEnum::EXPENSE->value ? 'danger' : 'success';
                    })
                    ->formatStateUsing(function ($record) {
                        return $record->type == TransactionTypeEnum::EXPENSE->value ? '-' . app_money($record->amount) : '+' . app_money($record->amount);
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_time')
                    ->label('Fecha y hora')
                    ->formatStateUsing(function ($record) {
                        return app_date($record->date_time);
                    })
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categoría')
                    ->multiple()
                    ->options(fn() => Category::select('id', 'name')->orderBy('name', 'asc')->pluck('name', 'id')->toArray())
                    ->attribute('category_id'),
                Tables\Filters\SelectFilter::make('account_id')
                    ->label('Cuenta')
                    ->multiple()
                    ->options(fn() => Account::select('id', 'name')->where('user_id', auth()->id())->orderBy('name', 'asc')->pluck('name', 'id')->toArray())
                    ->attribute('account_id'),
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->multiple()
                    ->options(TransactionTypeEnum::labels())
                    ->attribute('type'),
                Tables\Filters\Filter::make('date_time')
                    ->form([
                        Select::make('yearFilter')
                            ->label('Año')
                            ->placeholder('Todos')
                            ->options(years())
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                $set('monthFilter', '');
                            }),
                        Select::make('monthFilter')
                            ->label('Mes')
                            ->visible(fn(Get $get): bool => $get('yearFilter') != '')
                            ->placeholder('Todos')
                            ->options(months()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $year = $data['yearFilter'] ?? null;
                        $month = $data['monthFilter'] ?? null;

                        if ($year) {
                            if ($month) {
                                $startDate = toCarbon($year, $month)->startOfMonth();
                                $endDate = toCarbon($year, $month)->endOfMonth();
                            } else {
                                $startDate = toCarbon($year)->startOfYear();
                                $endDate = toCarbon($year)->endOfYear();
                            }

                            $query->whereBetween('date_time', [$startDate, $endDate]);
                        }

                        return $query;
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['yearFilter'] ?? null) {
                            $indicators['year'] = 'Año ' . $data['yearFilter'];
                        }

                        if ($data['monthFilter'] ?? null) {
                            $indicators['month'] = 'Mes ' . months()[$data['monthFilter']];
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function (Transaction $transaction) {
                        $transaction->account->actualBalance(true);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function (Collection $records) {
                            foreach ($records as $record) {
                                $record->account->actualBalance(true);
                            }
                        }),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            TransactionResource\Widgets\TransactionOverview::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('account', function ($query) {
                $query->where('user_id', auth()->id());
            });
    }
}
