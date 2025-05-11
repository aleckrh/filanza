<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\TransactionResource\Pages\ListTransactions;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    protected function getTablePage(): string
    {
        return ListTransactions::class;
    }

    protected function getStats(): array
    {
        $expenses = $this->getPageTableQuery()->where('type', TransactionTypeEnum::EXPENSE->value)->sum('amount');
        $incomes = $this->getPageTableQuery()->where('type', TransactionTypeEnum::INCOME->value)->sum('amount');

        return [
            Stat::make('Gastos', app_money($expenses))
                ->color('danger')
                ->icon('heroicon-m-arrow-trending-down'),
            Stat::make('Ingresos', app_money($incomes))
                ->color('success')
                ->icon('heroicon-m-arrow-trending-up'),
            Stat::make('Total de transacciones', $this->getPageTableQuery()->count())
                ->color('info')
                ->icon('heroicon-m-clock'),
        ];
    }
}
