<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatechesisSeasonResource\Pages;
use App\Models\CatechesisSeason;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CatechesisSeasonResource extends Resource
{
    protected static ?string $model = CatechesisSeason::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Catechisatie';

    protected static ?string $navigationLabel = 'Seizoenen';

    protected static ?string $modelLabel = 'Seizoen';

    protected static ?string $pluralModelLabel = 'Seizoenen';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user && $user->role === 'admin';
    }

    public static function canDelete(Model $record): bool
    {
        return $record->registrations()->doesntExist();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Seizoen')
                    ->placeholder('2026-2027')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->helperText('Bijvoorbeeld 2026-2027. Volgend jaar maakt u een nieuw seizoen aan, bijvoorbeeld 2027-2028.'),
                Forms\Components\Toggle::make('is_open')
                    ->label('Inschrijving open')
                    ->helperText('Als dit seizoen openstaat, kunnen mensen zich inschrijven. Andere seizoenen worden automatisch gesloten.')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Seizoen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_open')
                    ->label('Inschrijving open')
                    ->boolean(),
                Tables\Columns\TextColumn::make('registrations_count')
                    ->counts('registrations')
                    ->label('Inschrijvingen')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt op')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCatechesisSeasons::route('/'),
            'create' => Pages\CreateCatechesisSeason::route('/create'),
            'edit' => Pages\EditCatechesisSeason::route('/{record}/edit'),
        ];
    }
}
