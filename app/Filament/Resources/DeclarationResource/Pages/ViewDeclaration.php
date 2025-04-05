<?php

namespace App\Filament\Resources\DeclarationResource\Pages;

use App\Filament\Resources\DeclarationResource;
use Filament\Actions;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewDeclaration extends ViewRecord
{
    protected static string $resource = DeclarationResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Tabs::make('Declaration Details')
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('User'),
                                TextEntry::make('name')
                                    ->label('Name'),
                                TextEntry::make('street')
                                    ->label('Street'),
                                TextEntry::make('number')
                                    ->label('Number'),
                                TextEntry::make('zipcode')
                                    ->label('Zipcode'),
                                TextEntry::make('city')
                                    ->label('City'),
                                TextEntry::make('bankaccountnumber')
                                    ->label('Bank Account Number'),
                                TextEntry::make('amount')
                                    ->label('Amount')
                                    ->money('EUR'),
                                TextEntry::make('explanation')
                                    ->label('Explanation')
                                    ->columnSpanFull(),
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'warning',
                                    }),
                                TextEntry::make('admin_notes')
                                    ->label('Admin Notes')
                                    ->columnSpanFull(),
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime(),
                                TextEntry::make('updated_at')
                                    ->label('Updated At')
                                    ->dateTime(),
                            ]),
                        Tab::make('Attachments')
                            ->schema([
                                RepeatableEntry::make('attachments')
                                    ->schema([
                                        TextEntry::make('filename')
                                            ->label('Filename'),
                                        TextEntry::make('mime_type')
                                            ->label('Type'),
                                        TextEntry::make('size')
                                            ->label('Size')
                                            ->formatStateUsing(fn ($state) => number_format($state / 1024, 2) . ' KB'),
                                        TextEntry::make('created_at')
                                            ->label('Uploaded')
                                            ->dateTime(),
                                        ImageEntry::make('path')
                                            ->label('Preview')
                                            ->visible(fn ($record) => str_starts_with($record->mime_type, 'image/'))
                                            ->disk('public')
                                            ->openUrlInNewTab(),
                                        TextEntry::make('path')
                                            ->label('Download')
                                            ->formatStateUsing(fn ($state, $record) => 'Download')
                                            ->url(fn ($state, $record) => route('declarations.attachments.download', ['declaration' => $this->record, 'attachment' => $record]))
                                            ->openUrlInNewTab(),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
