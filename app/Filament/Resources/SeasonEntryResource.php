<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeasonEntryResource\Pages;
use App\Filament\Resources\SeasonEntryResource\RelationManagers;
use App\Models\SeasonEntry;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SeasonEntryResource extends Resource
{
    protected static ?string $model = SeasonEntry::class;

    protected static ?string $parentResource = SeasonResource::class;

    protected static ?string $modelLabel = 'Entry';

    protected static ?string $pluralModelLabel = 'Entries';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $recordTitleAttribute = 'reference';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Entry')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('reference')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\Select::make('season_id')
                            ->relationship('season', 'name')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('contact_name')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('contact_email')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('contact_telephone')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('venue_name')
                            ->label('Venue')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total')
                            ->prefix('£')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('payment_status')
                            ->label('Payment status')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('payment_provider')
                            ->label('Payment provider')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Paid at')
                            ->seconds(false),
                        Forms\Components\DateTimePicker::make('payment_completed_at')
                            ->label('Payment completed at')
                            ->seconds(false)
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('stripe_checkout_session_id')
                            ->label('Stripe checkout session')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('stripe_payment_intent_id')
                            ->label('Stripe payment intent')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contact_name')
                    ->label('Contact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('teams_count')
                    ->label('Teams')
                    ->counts('teams'),
                Tables\Columns\TextColumn::make('knockout_registrations_count')
                    ->label('Knockouts')
                    ->counts('knockoutRegistrations'),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('GBP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment status')
                    ->badge()
                    ->formatStateUsing(fn (SeasonEntry $record): string => $record->paymentStatusLabel())
                    ->color(fn (?string $state): string => match ($state) {
                        SeasonEntry::PAYMENT_STATUS_PAID => 'success',
                        SeasonEntry::PAYMENT_STATUS_CHECKOUT_CREATED => 'warning',
                        SeasonEntry::PAYMENT_STATUS_EXPIRED,
                        SeasonEntry::PAYMENT_STATUS_FAILED => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('paid_at')
                    ->label('Paid')
                    ->getStateUsing(fn (SeasonEntry $record): bool => $record->isPaid())
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('j M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('markPaid')
                    ->label('Mark paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (SeasonEntry $record): bool => ! $record->isPaid())
                    ->action(fn (SeasonEntry $record) => $record->markPaid('manual')),
                EditAction::make()->color('warning'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options(SeasonEntry::paymentStatusOptions()),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TeamsRelationManager::class,
            RelationManagers\KnockoutRegistrationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeasonEntries::route('/'),
            'edit' => Pages\EditSeasonEntry::route('/{record}/edit'),
        ];
    }
}
