<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\State;
use App\Models\City;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Laravel\Pail\Options;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;


class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema

            ->components([
                Grid::make()
                    ->columnSpan('full')
                    ->columns(1)
                    ->schema([
                        Section::make('Información del personal')
                            ->description('Descripción de ejemplo')
                            ->icon(Heroicon::UserPlus)
                            ->schema([
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->required(),
                                TextInput::make('password')
                                    ->password()
                                    ->required(),
                            ]),

                        Section::make('Información de dirección')
                            ->description('Descripción de ejemplo')
                            ->icon(Heroicon::MapPin)
                            ->schema([
                                Select::make('country_id')
                                    ->relationship(name: 'country', titleAttribute: 'name')
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('state_id', null);
                                        $set('city_id', null);
                                    })
                                    ->required(),

                                Select::make('state_id')
                                    ->options(fn(Get $get): array => State::query()
                                        ->where('country_id', $get('country_id'))
                                        ->pluck('name', 'id')
                                        ->toArray())
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('city_id', null);
                                    })
                                    ->required(),

                                Select::make('city_id')
                                    ->options(fn(Get $get): array => City::query()
                                        ->where('state_id', $get('state_id'))
                                        ->pluck('name', 'id')
                                        ->toArray())
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->required(),

                                Textarea::make('Address')
                                    ->live()
                                    ->required(),

                                TextInput::make('Postal Code')
                                    ->live()
                                    ->required(),

                            ])
                            ->columns(1)
                    ])
            ]);
    }
}
