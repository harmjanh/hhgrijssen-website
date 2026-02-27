<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgendaItemResource\Pages;
use App\Models\Agenda;
use App\Models\AgendaItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AgendaItemResource extends Resource
{
    protected static ?string $model = AgendaItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Agenda-items';

    protected static ?string $modelLabel = 'Agenda-item';

    protected static ?string $pluralModelLabel = 'Agenda-items';

    protected static ?int $navigationSort = 2;

    /**
     * Check if the user can view any records.
     * Only admin role can access this resource.
     */
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && $user->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Agenda-item gegevens')
                    ->schema([
                        Forms\Components\Select::make('agenda_id')
                            ->label('Agenda')
                            ->relationship('agenda', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('uid')
                            ->label('UID')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Unieke identifier (bijv. uit iCal)'),

                        Forms\Components\TextInput::make('title')
                            ->label('Titel')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Beschrijving')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('location')
                            ->label('Locatie')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\DateTimePicker::make('start_date')
                            ->label('Startdatum en -tijd')
                            ->required()
                            ->native(false)
                            ->displayFormat('d-m-Y H:i')
                            ->seconds(false),

                        Forms\Components\DateTimePicker::make('end_date')
                            ->label('Einddatum en -tijd')
                            ->required()
                            ->native(false)
                            ->displayFormat('d-m-Y H:i')
                            ->seconds(false)
                            ->after('start_date'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Einde')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titel')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('location')
                    ->label('Locatie')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('agenda.title')
                    ->label('Agenda')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('has_service')
                    ->label('Dienst')
                    ->getStateUsing(fn ($record) => $record->service ? 'Ja' : '—')
                    ->badge()
                    ->color(fn ($state, $record) => $record->service ? 'success' : 'gray'),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('agenda_id')
                    ->label('Agenda')
                    ->relationship('agenda', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgendaItems::route('/'),
            'history' => Pages\HistoryAgendaItems::route('/history'),
            'create' => Pages\CreateAgendaItem::route('/create'),
            'view' => Pages\ViewAgendaItem::route('/{record}'),
            'edit' => Pages\EditAgendaItem::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['agenda', 'service']);
    }
}
