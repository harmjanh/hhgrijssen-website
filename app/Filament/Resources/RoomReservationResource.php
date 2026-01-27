<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomReservationResource\Pages;
use App\Filament\Resources\RoomReservationResource\RelationManagers;
use App\Models\RoomReservation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Contracts\Auth\Authenticatable;

class RoomReservationResource extends Resource
{
    protected static ?string $model = RoomReservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Zaalreserveringen';
    protected static ?string $modelLabel = 'Zaalreservering';
    protected static ?string $pluralModelLabel = 'Zaalreserveringen';

    /**
     * Check if the user can view any records.
     * Only admin and koster roles can access this resource.
     */
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['admin', 'koster']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Gebruiker')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('room_id')
                    ->label('Zaal')
                    ->relationship('room', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('subject')
                    ->label('Onderwerp')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('number_of_people')
                    ->label('Aantal personen')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(100),
                Forms\Components\DateTimePicker::make('start_time')
                    ->label('Starttijd')
                    ->required()
                    ->native(false)
                    ->displayFormat('D d-m-Y H:i')
                    ->seconds(false),
                Forms\Components\DateTimePicker::make('end_time')
                    ->label('Eindtijd')
                    ->required()
                    ->native(false)
                    ->displayFormat('D d-m-Y H:i')
                    ->seconds(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Gebruiker')
                    ->searchable(),
                Tables\Columns\TextColumn::make('room.name')
                    ->label('Zaal')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Onderwerp')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('number_of_people')
                    ->label('Personen')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Starttijd')
                    ->formatStateUsing(fn ($state) => $state ? $state->locale('nl')->translatedFormat('D d-m-Y H:i') : '-'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Eindtijd')
                    ->formatStateUsing(fn ($state) => $state ? $state->locale('nl')->translatedFormat('D d-m-Y H:i') : '-'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->formatStateUsing(fn ($state) => $state ? $state->locale('nl')->translatedFormat('D d-m-Y H:i') : '-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('room')
                    ->label('Zaal')
                    ->relationship('room', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('user')
                    ->label('Gebruiker')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('start_time')
                    ->form([
                        Forms\Components\DatePicker::make('start_from')
                            ->label('Van datum'),
                        Forms\Components\DatePicker::make('start_until')
                            ->label('Tot datum'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_time', '>=', $date),
                            )
                            ->when(
                                $data['start_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('start_time', '<=', $date),
                            );
                    }),
            ])
            ->actions([
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
            'index' => Pages\ListRoomReservations::route('/'),
            'history' => Pages\HistoryRoomReservations::route('/history'),
            'create' => Pages\CreateRoomReservation::route('/create'),
            'edit' => Pages\EditRoomReservation::route('/{record}/edit'),
        ];
    }
}
