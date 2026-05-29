<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Forms\Components\Tabs::make('News Details')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, $set, $get, $record) {
                                        if (! $record || ! $record->exists) {
                                            $set('slug', \App\Models\News::generateUniqueSlug($state ?? ''));
                                        }
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Wordt automatisch gegenereerd op basis van de titel.'),
                                Forms\Components\FileUpload::make('image')
                                    ->nullable()
                                    ->directory('news')
                                    ->image(),
                                Forms\Components\Toggle::make('is_published')
                                    ->label('Published')
                                    ->default(false),
                                Forms\Components\Toggle::make('contact_form_enabled')
                                    ->label('Contactformulier tonen')
                                    ->default(false)
                                    ->live(),
                                Forms\Components\TextInput::make('contact_form_recipient')
                                    ->label('Ontvanger contactformulier (e-mailadres)')
                                    ->email()
                                    ->maxLength(255)
                                    ->placeholder('ontvanger@example.com')
                                    ->visible(fn ($get) => $get('contact_form_enabled'))
                                    ->required(fn ($get) => $get('contact_form_enabled')),
                                Forms\Components\DateTimePicker::make('visible_from')
                                    ->nullable(),
                                Forms\Components\DateTimePicker::make('visible_until')
                                    ->nullable(),
                                Forms\Components\Textarea::make('description')
                                    ->nullable()
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Tabs\Tab::make('Content')
                            ->schema([
                                Forms\Components\RichEditor::make('content')
                                    ->nullable(),
                            ])
                            ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),
                ImageColumn::make('image')
                    ->label('Image'),
                Tables\Columns\BooleanColumn::make('is_published')
                    ->label('Published')
                    ->sortable(),
                Tables\Columns\TextColumn::make('visible_from')
                    ->label('Visible From')
                    ->sortable(),
                Tables\Columns\TextColumn::make('visible_until')
                    ->label('Visible Until')
                    ->sortable(),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
