<?php

namespace App\Filament\Resources\Donors\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class DonorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->options(['individual' => 'Individual', 'company' => 'Company'])
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('whatsapp'),
                TextInput::make('facebook_url')
                    ->url(),
                TextInput::make('x_url')
                    ->url(),
                TextInput::make('linkedin_url')
                    ->url(),
                TextInput::make('instagram_url')
                    ->url(),
                TextInput::make('first_name'),
                TextInput::make('last_name'),
                DatePicker::make('birth_date'),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('company_name'),
                TextInput::make('ico'),
                TextInput::make('dic'),
                Textarea::make('legal_address')
                    ->columnSpanFull(),
                TextInput::make('representative_name'),
            ]);
    }
}
