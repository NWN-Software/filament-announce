<?php

namespace Rupadana\FilamentAnnounce\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\ViewAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Rupadana\FilamentAnnounce\Resources\AnnouncementResource\Pages\ListAnnouncements;
use Rupadana\FilamentAnnounce\Resources\AnnouncementResource\Pages\CreateAnnouncement;
use Rupadana\FilamentAnnounce\Resources\AnnouncementResource\Pages\ViewAnnouncement;
use App\Models\User;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Guava\FilamentIconPicker\Tables\IconColumn;
use Rupadana\FilamentAnnounce\Models\Announcement;
use Rupadana\FilamentAnnounce\Resources\AnnouncementResource\Pages;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-megaphone';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->minLength(5)
                    ->required()
                    ->translateLabel(),
                TextInput::make('title')
                    ->minLength(5)
                    ->required()
                    ->translateLabel(),
                Textarea::make('body')
                    ->minLength(20)
                    ->required()
                    ->translateLabel(),
                IconPicker::make('icon'),
                Select::make('color')
                    ->options([
                        ...collect(FilamentColor::getColors())->map(fn ($value, $key) => ucfirst($key))->toArray(),
                        'custom' => 'Custom',
                    ])
                    ->translateLabel()
                    ->live(),
                ColorPicker::make('custom_color')
                    ->hidden(fn (Get $get) => $get('color') != 'custom')
                    ->requiredIf('color', 'custom')
                    ->rgb()
                    ->translateLabel(),

                Select::make('users')
                    ->options(['all' => 'all'] + User::all()->pluck('name', 'id')->toArray())
                    ->multiple()
                    ->translateLabel()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->translateLabel(),
                TextColumn::make('title')
                    ->translateLabel(),
                TextColumn::make('body')
                    ->translateLabel(),
                IconColumn::make('icon')
                    ->translateLabel(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'view' => ViewAnnouncement::route('/{record}'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-announce.navigation.group');
    }

    public static function canAccess(): bool
    {
        if (method_exists(auth()->user(), 'hasRole')) {
            return auth()->user()->hasRole(config('filament-announce.can_access.role') ?? []);
        }

        return true;
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-announce.navigation.sort') ?? -1;
    }
}
