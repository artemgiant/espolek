<?php

namespace App\Filament\Resources\Donors\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DonorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Тип донора')
                    ->schema([
                        Select::make('type')
                            ->label('Тип')
                            ->options([
                                'individual' => 'Фізична особа',
                                'company' => 'Юридична особа',
                            ])
                            ->required()
                            ->live()
                            ->default('individual'),
                    ]),

                Section::make('Фізична особа')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('Ім\'я')
                            ->required(),
                        TextInput::make('last_name')
                            ->label('Прізвище')
                            ->required(),
                        DatePicker::make('birth_date')
                            ->label('Дата народження'),
                        Textarea::make('address')
                            ->label('Адреса проживання')
                            ->rows(2),
                    ])
                    ->columns(2)
                    ->visible(fn ($get) => $get('type') === 'individual'),

                Section::make('Юридична особа')
                    ->schema([
                        TextInput::make('company_name')
                            ->label('Назва організації')
                            ->required(),
                        TextInput::make('ico')
                            ->label('IČO'),
                        TextInput::make('dic')
                            ->label('DIČ'),
                        Textarea::make('legal_address')
                            ->label('Юридична адреса')
                            ->rows(2),
                        TextInput::make('representative_name')
                            ->label('Ім\'я представника'),
                    ])
                    ->columns(2)
                    ->visible(fn ($get) => $get('type') === 'company'),

                Section::make('Контакти')
                    ->schema([
                        TextInput::make('email')
                            ->label('Email')
                            ->email(),
                        TextInput::make('phone')
                            ->label('Телефон')
                            ->tel(),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp'),
                    ])
                    ->columns(3),

                Section::make('Соціальні мережі')
                    ->schema([
                        TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->prefix('https://'),
                        TextInput::make('x_url')
                            ->label('X (Twitter)')
                            ->url()
                            ->prefix('https://'),
                        TextInput::make('linkedin_url')
                            ->label('LinkedIn')
                            ->url()
                            ->prefix('https://'),
                        TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->prefix('https://'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Банківські рахунки')
                    ->schema([
                        Repeater::make('bankAccounts')
                            ->relationship()
                            ->label('')
                            ->schema([
                                TextInput::make('account_number')
                                    ->label('Номер рахунку')
                                    ->required(),
                                TextInput::make('bank_code')
                                    ->label('Код банку'),
                                TextInput::make('bank_name')
                                    ->label('Назва банку'),
                                TextInput::make('iban')
                                    ->label('IBAN'),
                            ])
                            ->columns(4)
                            ->addActionLabel('Додати рахунок')
                            ->defaultItems(0),
                    ]),
            ]);
    }
}
