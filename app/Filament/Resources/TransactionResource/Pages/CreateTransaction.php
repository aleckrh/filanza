<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $account = $this->record->account;

        if ($this->data['type'] == TransactionTypeEnum::INCOME->value) {
            $account->balance += floatval($this->data['amount']);
            $account->save();
        }

        if ($this->data['type'] == TransactionTypeEnum::EXPENSE->value) {
            $account->balance -= floatval($this->data['amount']);
            $account->save();
        }
    }
}
