<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use App\Models\AgendaItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Services';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Service Details')
                    ->schema([
                        Forms\Components\Select::make('agenda_item_id')
                            ->label('Agenda Item')
                            ->options(AgendaItem::whereDoesntHave('service')->pluck('title', 'id'))
                            ->searchable()
                            ->required()
                            ->placeholder('Select an agenda item')
                            ->helperText('Choose an agenda item that doesn\'t already have a service'),

                        Forms\Components\TextInput::make('pastor')
                            ->label('Pastor')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter pastor name'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Service Information')
                    ->schema([
                        Forms\Components\RichEditor::make('liturgy')
                            ->label('Liturgy / Order of Service')
                            ->nullable()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'link',
                                'undo',
                                'redo',
                            ])
                            ->placeholder('Enter the liturgy or order of service...'),

                        Forms\Components\TextInput::make('youtube_url')
                            ->label('YouTube URL')
                            ->url()
                            ->nullable()
                            ->maxLength(255)
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->helperText('Enter the full YouTube URL for the service recording'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agendaItem.start_date')
                    ->label('Service Date & Time')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->searchable()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('agendaItem.title')
                    ->label('Agenda Item')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('pastor')
                    ->label('Pastor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('liturgy')
                    ->label('Liturgy')
                    ->limit(50)
                    ->html()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('youtube_url')
                    ->label('YouTube')
                    ->url(fn ($record) => $record->youtube_url)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn ($state) => $state ? 'View Recording' : 'No Recording')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('agendaItem.start_date', 'desc')
            ->filters([
                Tables\Filters\Filter::make('upcoming_services')
                    ->label('Upcoming Services')
                    ->query(fn (Builder $query): Builder => $query->whereHas('agendaItem', function ($q) {
                        $q->where('start_date', '>=', now());
                    }))
                    ->toggle(),

                Tables\Filters\Filter::make('past_services')
                    ->label('Past Services')
                    ->query(fn (Builder $query): Builder => $query->whereHas('agendaItem', function ($q) {
                        $q->where('start_date', '<', now());
                    }))
                    ->toggle(),

                Tables\Filters\SelectFilter::make('pastor')
                    ->options(fn () => Service::distinct()->pluck('pastor', 'pastor')->toArray())
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('agendaItem')
            ->orderBy('agendaItem.start_date', 'desc');
    }
}
