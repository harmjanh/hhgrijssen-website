<?php

namespace App\Filament\Resources;

use App\Exports\ServicesExport;
use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use App\Models\AgendaItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

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
                            ->options(function ($get, $record) {
                                $query = AgendaItem::query();

                                // Exclude agenda items that already have a service
                                // But include the current service's agenda item when editing
                                $query->where(function ($q) use ($record) {
                                    $q->whereDoesntHave('service');

                                    // Include the current service's agenda item when editing
                                    if ($record && $record->agenda_item_id) {
                                        $q->orWhere('id', $record->agenda_item_id);
                                    }
                                });

                                // Order by start date descending (most recent first)
                                $query->orderBy('start_date', 'desc');

                                // Build options array with title and date
                                return $query->get()->mapWithKeys(function ($item) {
                                    return [$item->id => "{$item->title} - {$item->start_date->format('d-m-Y H:i')}"];
                                })->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if ($state) {
                                    $agendaItem = AgendaItem::find($state);
                                    if ($agendaItem) {
                                        $set('pastor', $agendaItem->title);
                                    }
                                }
                            })
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

                Tables\Filters\Filter::make('service_date')
                    ->label('Service Datum')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Van datum'),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Tot datum'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $dateFrom = $data['date_from'] ?? null;
                        $dateUntil = $data['date_until'] ?? null;

                        return $query->whereHas('agendaItem', function ($q) use ($dateFrom, $dateUntil) {
                            if ($dateFrom && $dateUntil) {
                                // Find services that overlap with the date range
                                // Service overlaps if: start_date <= date_until AND end_date >= date_from
                                $q->whereDate('start_date', '<=', $dateUntil)
                                  ->whereDate('end_date', '>=', $dateFrom);
                            } elseif ($dateFrom) {
                                // Only date_from: find services that start or end on/after this date
                                $q->where(function ($subQuery) use ($dateFrom) {
                                    $subQuery->whereDate('start_date', '>=', $dateFrom)
                                             ->orWhereDate('end_date', '>=', $dateFrom);
                                });
                            } elseif ($dateUntil) {
                                // Only date_until: find services that start or end on/before this date
                                $q->where(function ($subQuery) use ($dateUntil) {
                                    $subQuery->whereDate('start_date', '<=', $dateUntil)
                                             ->orWhereDate('end_date', '<=', $dateUntil);
                                });
                            }
                        });
                    }),
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
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export naar Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')
                            ->label('Van datum')
                            ->required()
                            ->displayFormat('d-m-Y')
                            ->native(false),
                        Forms\Components\DatePicker::make('date_until')
                            ->label('Tot datum')
                            ->required()
                            ->displayFormat('d-m-Y')
                            ->native(false)
                            ->after('date_from'),
                    ])
                    ->action(function (array $data) {
                        $dateFrom = $data['date_from'];
                        $dateUntil = $data['date_until'];

                        $export = new ServicesExport($dateFrom, $dateUntil);
                        $filename = 'diensten_' . \Carbon\Carbon::parse($dateFrom)->format('Y-m-d') . '_' . \Carbon\Carbon::parse($dateUntil)->format('Y-m-d') . '.xlsx';

                        return Excel::download($export, $filename);
                    })
                    ->modalHeading('Export Diensten naar Excel')
                    ->modalDescription('Selecteer een datum-range om de diensten te exporteren.')
                    ->modalSubmitActionLabel('Exporteren'),
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
            ->join('agenda_items', 'services.agenda_item_id', '=', 'agenda_items.id')
            ->select('services.*')
            ->orderBy('agenda_items.start_date', 'desc');
    }

    public static function resolveRecordRouteBinding($key): ?Service
    {
        // Don't use the join when resolving a single record to avoid ambiguous id column
        return parent::getEloquentQuery()
            ->where('services.id', $key)
            ->first();
    }
}
