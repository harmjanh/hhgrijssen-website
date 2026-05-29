<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TreatOrderResource\Pages;
use App\Models\TreatOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TreatOrderResource extends Resource
{
    protected static ?string $model = TreatOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Traktatiebestellingen';

    protected static ?string $modelLabel = 'Traktatiebestelling';

    protected static ?string $pluralModelLabel = 'Traktatiebestellingen';

    protected static ?int $navigationSort = 8;

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user && $user->role === 'admin';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Naam')
                    ->disabled(),
                Forms\Components\TextInput::make('email')
                    ->label('E-mail')
                    ->disabled(),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefoon')
                    ->disabled(),
                Forms\Components\TextInput::make('snoeprollen_quantity')
                    ->label('Snoeprollen (×10)')
                    ->disabled(),
                Forms\Components\TextInput::make('stroopwafels_quantity')
                    ->label('Stroopwafels (×3 pakjes)')
                    ->disabled(),
                Forms\Components\TextInput::make('total_amount')
                    ->label('Totaalbedrag')
                    ->prefix('€')
                    ->disabled(),
                Forms\Components\Textarea::make('remarks')
                    ->label('Opmerkingen')
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Naam')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefoon')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('snoeprollen_quantity')
                    ->label('Snoeprollen')
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "{$state}× (10 stuks)" : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stroopwafels_quantity')
                    ->label('Stroopwafels')
                    ->formatStateUsing(fn (int $state): string => $state > 0 ? "{$state}× (3 pakjes)" : '—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Totaal')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'Betaald',
                        'pending' => 'In behandeling',
                        'failed' => 'Mislukt',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Besteld op')
                    ->dateTime('d-m-Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'paid' => 'Betaald',
                        'pending' => 'In behandeling',
                        'failed' => 'Mislukt',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListTreatOrders::route('/'),
            'view' => Pages\ViewTreatOrder::route('/{record}'),
        ];
    }
}
