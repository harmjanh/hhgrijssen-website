<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Resources\PageResource\RelationManagers;
use App\Models\Page;
use App\Models\PageType;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
                                    ->maxLength(255)
                                    ->afterStateUpdated(function (string $state, Forms\Set $set, ?string $operation) {
                                            $set('slug', Str::slug($state));
                                    }),
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
                                            throw new \Exception("The selected page type has reached its maximum count of pages.");
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
            'index' => Pages\ListPages::route('/'),
            // 'create' => Pages\CreatePage::route('/create'),
            'edit'  => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
