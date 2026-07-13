<?php

namespace App\Filament\Resources\NewsResource\Pages;

use App\Filament\Resources\NewsResource;
use Filament\Actions;
use Filament\Actions\ReplicateAction;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;

class EditNews extends EditRecord
{
    protected static string $resource = NewsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ReplicateAction::make('copy')
                ->label('Kopieer')
                ->excludeAttributes(['slug'])
                ->mutateRecordDataUsing(function (array $data): array {
                    $data['title'] = isset($data['title']) ? ($data['title'] . ' (kopie)') : $data['title'];
                    $data['is_published'] = false;
                    $data['visible_from'] = null;
                    $data['visible_until'] = null;

                    return $data;
                })
                ->successRedirectUrl(fn (Model $replica): string => NewsResource::getUrl('edit', ['record' => $replica])),
            Actions\DeleteAction::make(),
        ];
    }
}
