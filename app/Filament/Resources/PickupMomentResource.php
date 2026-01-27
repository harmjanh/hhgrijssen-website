<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PickupMomentResource\Pages;
use App\Filament\Resources\PickupMomentResource\RelationManagers;
use App\Models\PickupMoment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PickupMomentResource extends Resource
{
    protected static ?string $model = PickupMoment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    
    protected static ?string $navigationLabel = 'Afhaalmomenten';
    
    protected static ?string $modelLabel = 'Afhaalmoment';
    
    protected static ?string $pluralModelLabel = 'Afhaalmomenten';

    /**
     * Check if the user can view any records.
     * Only admin and muntenuitgifte roles can access this resource.
     */
    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['admin', 'muntenuitgifte']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date')
                    ->label('Datum')
                    ->required()
                    ->native(false)
                    ->displayFormat('d-m-Y')
                    ->default(now())
                    ->helperText('Afhaalmomenten zijn altijd om 10:00'),
                Forms\Components\Toggle::make('active')
                    ->label('Actief')
                    ->default(true)
                    ->helperText('Alleen actieve afhaalmomenten zijn beschikbaar voor selectie'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Datum')
                    ->date('d-m-Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('time')
                    ->label('Tijd')
                    ->default('10:00')
                    ->sortable(false),
                Tables\Columns\IconColumn::make('active')
                    ->label('Actief')
                    ->boolean(),
                Tables\Columns\TextColumn::make('coin_orders_count')
                    ->label('Aantal bestellingen')
                    ->counts('coinOrders')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Actief')
                    ->placeholder('Alle')
                    ->trueLabel('Alleen actieve')
                    ->falseLabel('Alleen inactieve'),
            ])
            ->actions([
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
        return [
            RelationManagers\CoinOrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPickupMoments::route('/'),
            'create' => Pages\CreatePickupMoment::route('/create'),
            'edit' => Pages\EditPickupMoment::route('/{record}/edit'),
        ];
    }
}
