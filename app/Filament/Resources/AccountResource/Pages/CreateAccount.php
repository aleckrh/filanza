<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Enums\TransactionTypeEnum;
use App\Filament\Resources\AccountResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        if ($data['is_default']) {
            static::getModel()::where('is_default', true)->where('user_id', auth()->id())->update(['is_default' => false]);
        }

        return static::getModel()::create($data);
    }

    protected function afterCreate(): void
    {
        $this->record->transactions()->create([
            'name' => 'Monto inicial',
            'description' => 'Se creo al registrar la cuenta',
            'amount' => $this->record->balance,
            'type' => TransactionTypeEnum::INCOME->value,
            'category_id' => 1,
            'account_id' => $this->record->id,
        ]);
    }
}
