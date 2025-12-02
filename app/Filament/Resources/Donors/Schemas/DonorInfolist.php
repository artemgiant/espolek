<?php

namespace App\Filament\Resources\Donors\Schemas;

use App\Models\Donor;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DonorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Основна інформація')
                    ->schema([
                        TextEntry::make('type')
                            ->label('Тип')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'individual' => 'Фізична особа',
                                'company' => 'Юридична особа',
                                default => $state,
                            }),
                        TextEntry::make('display_name')
                            ->label('Ім\'я / Назва'),
                    ])
                    ->columns(2),

                Section::make('Фізична особа')
                    ->schema([
                        TextEntry::make('first_name')
                            ->label('Ім\'я'),
                        TextEntry::make('last_name')
                            ->label('Прізвище'),
                        TextEntry::make('birth_date')
                            ->label('Дата народження')
                            ->date()
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->label('Адреса')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->visible(fn (Donor $record): bool => $record->isIndividual()),

                Section::make('Юридична особа')
                    ->schema([
                        TextEntry::make('company_name')
                            ->label('Назва організації'),
                        TextEntry::make('ico')
                            ->label('IČO')
                            ->placeholder('-'),
                        TextEntry::make('dic')
                            ->label('DIČ')
                            ->placeholder('-'),
                        TextEntry::make('legal_address')
                            ->label('Юридична адреса')
                            ->placeholder('-'),
                        TextEntry::make('representative_name')
                            ->label('Представник')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->visible(fn (Donor $record): bool => $record->isCompany()),

                Section::make('Контакти')
                    ->schema([
                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->label('Телефон')
                            ->placeholder('-'),
                        TextEntry::make('whatsapp')
                            ->label('WhatsApp')
                            ->placeholder('-'),
                    ])
                    ->columns(3),

                Section::make('Соціальні мережі')
                    ->schema([
                        TextEntry::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->placeholder('-'),
                        TextEntry::make('x_url')
                            ->label('X (Twitter)')
                            ->url()
                            ->placeholder('-'),
                        TextEntry::make('linkedin_url')
                            ->label('LinkedIn')
                            ->url()
                            ->placeholder('-'),
                        TextEntry::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('Банківські рахунки')
                    ->schema([
                        RepeatableEntry::make('bankAccounts')
                            ->label('')
                            ->schema([
                                TextEntry::make('account_number')
                                    ->label('Номер рахунку'),
                                TextEntry::make('bank_code')
                                    ->label('Код банку')
                                    ->placeholder('-'),
                                TextEntry::make('bank_name')
                                    ->label('Назва банку')
                                    ->placeholder('-'),
                            ])
                            ->columns(3),
                    ]),

                Section::make('Системна інформація')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Створено')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Оновлено')
                            ->dateTime(),
                        TextEntry::make('deleted_at')
                            ->label('Видалено')
                            ->dateTime()
                            ->visible(fn (Donor $record): bool => $record->trashed()),
                    ])
                    ->columns(2),
            ]);
    }
}
