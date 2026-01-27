<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScipioRegistrationResource\Pages;
use App\Models\ScipioRegistration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ScipioRegistrationResource extends Resource
{
    protected static ?string $model = ScipioRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    
    protected static ?string $navigationLabel = 'Scipio Registraties';
    
    protected static ?string $modelLabel = 'Scipio Registratie';
    
    protected static ?string $pluralModelLabel = 'Scipio Registraties';

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
                Forms\Components\TextInput::make('regnr')
                    ->label('Regnr')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phonenumber')
                    ->label('Telefoon')
                    ->maxLength(255),
                Forms\Components\TextInput::make('mobile')
                    ->label('Mobiel')
                    ->maxLength(255),
                Forms\Components\Toggle::make('has_solidarity_fund')
                    ->label('Solidariteitsfonds')
                    ->default(false),
                Forms\Components\Toggle::make('has_zaaier')
                    ->label('De Zaaier')
                    ->default(false),
                Forms\Components\Toggle::make('has_privacy_consent')
                    ->label('Privacy toestemming')
                    ->default(false),
                Forms\Components\Toggle::make('has_vwb')
                    ->label('VWB')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('regnr')
                    ->label('Regnr')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phonenumber')
                    ->label('Telefoon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mobile')
                    ->label('Mobiel')
                    ->searchable(),
                Tables\Columns\IconColumn::make('has_solidarity_fund')
                    ->label('Solidariteitsfonds')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_zaaier')
                    ->label('De Zaaier')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_privacy_consent')
                    ->label('Privacy')
                    ->boolean(),
                Tables\Columns\IconColumn::make('has_vwb')
                    ->label('VWB')
                    ->boolean(),
                Tables\Columns\TextColumn::make('imported_at')
                    ->label('Geïmporteerd op')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt op')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Bijgewerkt op')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('imported_at', 'desc');
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
            'index' => Pages\ListScipioRegistrations::route('/'),
            'create' => Pages\CreateScipioRegistration::route('/create'),
            'edit' => Pages\EditScipioRegistration::route('/{record}/edit'),
        ];
    }
}
