<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Table;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

    protected static string|\UnitEnum|null $navigationGroup = 'Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Information')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('featured_image')
                            ->label('Featured image')
                            ->collection('featured-images')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->placeholder('News title')
                            ->columnSpanFull(),
                        Forms\Components\Hidden::make('published_at'),
                        Forms\Components\ToggleButtons::make('publication_status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->inline()
                            ->live()
                            ->default('draft')
                            ->dehydrated(false)
                            ->afterStateHydrated(function (Forms\Components\ToggleButtons $component, ?News $record, callable $set): void {
                                if ($record?->published_at) {
                                    $set('published_at', $record->published_at->toDateTimeString());
                                    $component->state('published');

                                    return;
                                }

                                $component->state('draft');
                            })
                            ->afterStateUpdated(function (?string $state, callable $get, callable $set): void {
                                if ($state === 'published') {
                                    $set('published_at', $get('published_at') ?: now()->toDateTimeString());

                                    return;
                                }

                                $set('published_at', null);
                            })
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('content')
                            ->rows(10)
                            ->label('Content')
                            ->required()
                            ->placeholder('News content')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('featured_image')
                    ->label('Image')
                    ->collection('featured-images')
                    ->square(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? 'Published' : 'Draft')
                    ->color(fn (?string $state): string => filled($state) ? 'success' : 'gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->date()
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
