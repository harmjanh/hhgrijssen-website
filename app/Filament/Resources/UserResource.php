<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Gebruikers';
    protected static ?string $modelLabel = 'Gebruiker';
    protected static ?string $pluralModelLabel = 'Gebruikers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basisinformatie')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Naam')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('E-mail geverifieerd op')
                            ->displayFormat('d-m-Y H:i')
                            ->native(false)
                            ->helperText('Laat leeg als e-mail nog niet geverifieerd is.'),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Geboortedatum')
                            ->displayFormat('d-m-Y')
                            ->native(false),
                        Forms\Components\Select::make('role')
                            ->label('Rol')
                            ->options([
                                'admin' => 'Admin',
                                'user' => 'Gebruiker',
                            ])
                            ->default('user'),
                        Forms\Components\Toggle::make('is_blocked')
                            ->label('Account blokkeren')
                            ->helperText('Als dit is ingeschakeld, kan de gebruiker niet meer inloggen.')
                            ->dehydrated(false)
                            ->afterStateUpdated(function ($state, Forms\Set $set, $get) {
                                if ($state) {
                                    $set('blocked_at', now());
                                } else {
                                    $set('blocked_at', null);
                                }
                            })
                            ->default(fn ($record) => $record?->isBlocked() ?? false),
                        Forms\Components\DateTimePicker::make('blocked_at')
                            ->label('Geblokkeerd op')
                            ->displayFormat('d-m-Y H:i')
                            ->native(false)
                            ->visible(fn ($get) => $get('blocked_at') !== null)
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('password')
                            ->label('Wachtwoord')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->minLength(8)
                            ->maxLength(255)
                            ->helperText('Laat leeg bij bewerken om het huidige wachtwoord te behouden.'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Adresgegevens')
                    ->schema([
                        Forms\Components\TextInput::make('street')
                            ->label('Straat')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('number')
                            ->label('Huisnummer')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('zipcode')
                            ->label('Postcode')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->label('Plaats')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Contactgegevens')
                    ->schema([
                        Forms\Components\TextInput::make('phonenumber')
                            ->label('Telefoonnummer')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('bankaccountnumber')
                            ->label('Bankrekeningnummer')
                            ->password()
                            ->maxLength(255)
                            ->helperText('Het bankrekeningnummer wordt versleuteld opgeslagen.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'user' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Admin',
                        'user' => 'Gebruiker',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->label('Plaats')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phonenumber')
                    ->label('Telefoonnummer')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('blocked_at')
                    ->label('Geblokkeerd')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-exclamation')
                    ->falseIcon('heroicon-o-shield-check')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->getStateUsing(fn (User $record): bool => $record->isBlocked())
                    ->sortable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('E-mail geverifieerd')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Aangemaakt')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rol')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'Gebruiker',
                    ]),
                Tables\Filters\TernaryFilter::make('blocked_at')
                    ->label('Geblokkeerd')
                    ->placeholder('Alle gebruikers')
                    ->trueLabel('Alleen geblokkeerde gebruikers')
                    ->falseLabel('Alleen niet-geblokkeerde gebruikers')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('blocked_at'),
                        false: fn (Builder $query) => $query->whereNull('blocked_at'),
                        blank: fn (Builder $query) => $query,
                    )
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle_block')
                    ->label(fn (User $record) => $record->isBlocked() ? 'Deblokkeren' : 'Blokkeren')
                    ->icon(fn (User $record) => $record->isBlocked() ? 'heroicon-o-shield-check' : 'heroicon-o-shield-exclamation')
                    ->color(fn (User $record) => $record->isBlocked() ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record) => $record->isBlocked() ? 'Account deblokkeren' : 'Account blokkeren')
                    ->modalDescription(fn (User $record) => $record->isBlocked() 
                        ? "Weet u zeker dat u het account van {$record->name} wilt deblokkeren? De gebruiker kan dan weer inloggen."
                        : "Weet u zeker dat u het account van {$record->name} wilt blokkeren? De gebruiker kan dan niet meer inloggen.")
                    ->action(function (User $record) {
                        if ($record->isBlocked()) {
                            $record->update(['blocked_at' => null]);
                            \Filament\Notifications\Notification::make()
                                ->title('Account gedeblokkeerd')
                                ->success()
                                ->send();
                        } else {
                            $record->update(['blocked_at' => now()]);
                            \Filament\Notifications\Notification::make()
                                ->title('Account geblokkeerd')
                                ->warning()
                                ->send();
                        }
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
