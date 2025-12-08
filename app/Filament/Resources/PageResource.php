<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use App\Models\PageType;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Page Details')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(Page::class, 'slug', ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('header_image')
                                    ->nullable()
                                    ->image()
                                    ->imageEditor(),
                                Forms\Components\Select::make('page_type_id')
                                    ->relationship('pageType', 'name')
                                    ->required()
                                    ->reactive()
                                    ->options(function ($get, $record) {
                                        $query = PageType::query();

                                        $query->whereNull('max_count')
                                            ->orWhere(function (Builder $query) {
                                                $query->where('max_count', '>', DB::raw('(SELECT COUNT(*) FROM pages WHERE page_type_id = page_types.id)'));
                                            });

                                        // Ensure the current page's pageType is included
                                        if ($record && $record->page_type_id) {
                                            $query->orWhere('id', $record->page_type_id);
                                        }

                                        return $query->pluck('name', 'id');
                                    })
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        /** @var PageType $pageType */
                                        $pageType = PageType::find($state);
                                        if ($pageType && $pageType->max_count !== null && $pageType->pages()->count() >= $pageType->max_count) {
                                            $set('page_type_id', null);
                                            throw new Exception('The selected page type has reached its maximum count of pages.');
                                        }
                                    }),
                                Forms\Components\Select::make('parent_id')
                                    ->relationship('parent', 'title')
                                    ->label('Parent Page')
                                    ->nullable()
                                    ->options(function ($get, $record) {
                                        // Exclude the current page from parent options to prevent self-reference
                                        $query = Page::query();
                                        if ($record) {
                                            $query->where('id', '!=', $record->id);
                                        }

                                        return $query->pluck('title', 'id');
                                    }),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('Sorteervolgorde')
                                    ->helperText('Gebruik dit nummer om de volgorde van child pagina\'s te bepalen. Lagere nummers verschijnen eerst in het navigatiemenu.')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                Forms\Components\Checkbox::make('exclude_from_navigation')
                                    ->label('Uitsluiten van navigatie')
                                    ->helperText('Als deze optie is aangevinkt, wordt deze pagina niet getoond in de navigatie.')
                                    ->default(false),
                                Forms\Components\Checkbox::make('requires_authentication')
                                    ->label('Alleen voor ingelogde gebruikers')
                                    ->helperText('Als deze optie is aangevinkt, is deze pagina alleen toegankelijk voor ingelogde gebruikers en wordt getoond in de ingelogde omgeving.')
                                    ->default(false),
                            ]),
                        Forms\Components\Tabs\Tab::make('Content')
                            ->schema([
                                Forms\Components\RichEditor::make('content')
                                    ->nullable()
                                    ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),
                            ])->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('pageType.name'),
                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Parent Page'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Sorteervolgorde')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\IconColumn::make('exclude_from_navigation')
                    ->label('Uit navigatie')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
                Tables\Columns\IconColumn::make('requires_authentication')
                    ->label('Authenticatie vereist')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('warning')
                    ->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Parent Pagina')
                    ->relationship('parent', 'title')
                    ->searchable()
                    ->preload(),
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
            ->defaultSort('sort_order', 'asc');
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
            'index' => Pages\ListPages::route('/'),
            // 'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
