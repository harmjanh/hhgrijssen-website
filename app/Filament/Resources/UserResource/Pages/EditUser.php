<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove password from data if it's empty (don't update password)
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            // Hash the password if it's provided
            $data['password'] = Hash::make($data['password']);
        }

        return $data;
    }
}
