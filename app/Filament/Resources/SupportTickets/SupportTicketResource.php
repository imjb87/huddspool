<?php

namespace App\Filament\Resources\SupportTickets;

use App\Enums\SupportTicketStatus;
use App\Filament\Resources\SupportTickets\Pages\EditSupportTicket;
use App\Filament\Resources\SupportTickets\Pages\ListSupportTickets;
use App\Filament\Resources\SupportTickets\Pages\ViewSupportTicket;
use App\Models\SupportTicket;
use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string|\UnitEnum|null $navigationGroup = 'Support';

    protected static ?string $recordTitleAttribute = 'ticket_number';

    public static function getModelLabel(): string
    {
        return 'Support ticket';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Support tickets';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = SupportTicket::query()->whereIn('status', SupportTicketStatus::openValues())->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request')
                ->schema([
                    Forms\Components\TextInput::make('ticket_number')
                        ->label('Ticket')
                        ->disabled()
                        ->dehydrated(false),
                    Forms\Components\TextInput::make('requester_name')
                        ->label('Name')
                        ->disabled()
                        ->dehydrated(false),
                    Forms\Components\TextInput::make('requester_email')
                        ->label('Email')
                        ->email()
                        ->disabled()
                        ->dehydrated(false),
                    Forms\Components\Textarea::make('message')
                        ->label('Message')
                        ->rows(8)
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
            Section::make('Workflow')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options(SupportTicketStatus::options())
                        ->required(),
                ])
                ->columns(1)
                ->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request')
                ->schema([
                    TextEntry::make('ticket_number')->label('Ticket'),
                    TextEntry::make('requester_name')->label('Name'),
                    TextEntry::make('requester_email')->label('Email'),
                    TextEntry::make('message')
                        ->label('Message')
                        ->prose()
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
            Section::make('Workflow')
                ->schema([
                    TextEntry::make('status')
                        ->badge()
                        ->formatStateUsing(fn (SupportTicketStatus|string|null $state): string => $state instanceof SupportTicketStatus ? $state->label() : (SupportTicketStatus::tryFrom((string) $state)?->label() ?? 'Unknown'))
                        ->color(fn (SupportTicketStatus|string|null $state): string => ($state instanceof SupportTicketStatus ? $state : SupportTicketStatus::tryFrom((string) $state))?->color() ?? 'gray'),
                    TextEntry::make('last_activity_at')->label('Last activity')->dateTime('j M Y H:i'),
                ])
                ->columns(2)
                ->columnSpanFull(),
            Section::make('Conversation')
                ->schema([
                    RepeatableEntry::make('replies')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('user.name')->label('From')->placeholder('Deleted user'),
                            TextEntry::make('created_at')->label('Sent')->dateTime('j M Y H:i'),
                            TextEntry::make('content')->prose()->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Ticket')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('j M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_activity_at')->label('Last activity')->dateTime('j M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (SupportTicketStatus|string|null $state): string => $state instanceof SupportTicketStatus ? $state->label() : (SupportTicketStatus::tryFrom((string) $state)?->label() ?? 'Unknown'))
                    ->color(fn (SupportTicketStatus|string|null $state): string => ($state instanceof SupportTicketStatus ? $state : SupportTicketStatus::tryFrom((string) $state))?->color() ?? 'gray')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(SupportTicketStatus::options()),
            ])
            ->recordUrl(fn (SupportTicket $record): string => static::getUrl('view', ['record' => $record]))
            ->recordActions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('last_activity_at', 'desc');
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
            'index' => ListSupportTickets::route('/'),
            'view' => ViewSupportTicket::route('/{record}'),
            'edit' => EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
