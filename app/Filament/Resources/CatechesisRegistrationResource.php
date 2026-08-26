<?php

namespace App\Filament\Resources;

use App\Enums\CatechesisGroup;
use App\Filament\Resources\CatechesisRegistrationResource\Pages;
use App\Models\CatechesisRegistration;
use App\Models\CatechesisSeason;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CatechesisRegistrationResource extends Resource
{
    protected static ?string $model = CatechesisRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Catechisatie';

    protected static ?string $navigationLabel = 'Inschrijvingen';

    protected static ?string $modelLabel = 'Inschrijving';

    protected static ?string $pluralModelLabel = 'Inschrijvingen';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user && $user->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('season_id')
                    ->label('Seizoen')
                    ->relationship('season', 'name')
                    ->default(fn () => CatechesisSeason::current()?->id)
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('first_name')
                    ->label('Voornaam')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('last_name')
                    ->label('Achternaam')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('E-mailadres')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefoonnummer')
                    ->tel()
                    ->required()
                    ->maxLength(50),
                Forms\Components\Select::make('group')
                    ->label('Groep')
                    ->options(CatechesisGroup::groupedOptions())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Voornaam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->label('Achternaam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mailadres')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefoonnummer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('group')
                    ->label('Groep')
                    ->formatStateUsing(fn (CatechesisGroup $state): string => $state->label())
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('season.name')
                    ->label('Seizoen')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ingeschreven op')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('season_id')
                    ->label('Seizoen')
                    ->relationship('season', 'name')
                    ->default(fn (): ?int => CatechesisSeason::current()?->id)
                    ->preload(),
                Tables\Filters\SelectFilter::make('group')
                    ->label('Groep')
                    ->options(CatechesisGroup::options()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('season'));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCatechesisRegistrations::route('/'),
            'create' => Pages\CreateCatechesisRegistration::route('/create'),
            'view' => Pages\ViewCatechesisRegistration::route('/{record}'),
            'edit' => Pages\EditCatechesisRegistration::route('/{record}/edit'),
        ];
    }
}
