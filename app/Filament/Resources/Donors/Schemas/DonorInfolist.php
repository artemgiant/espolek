<?php

namespace App\Filament\Resources\Donors\Schemas;

use App\Models\Donor;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DonorInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('type')
                    ->badge(),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('whatsapp')
                    ->placeholder('-'),
                TextEntry::make('facebook_url')
                    ->placeholder('-'),
                TextEntry::make('x_url')
                    ->placeholder('-'),
                TextEntry::make('linkedin_url')
                    ->placeholder('-'),
                TextEntry::make('instagram_url')
                    ->placeholder('-'),
                TextEntry::make('first_name')
                    ->placeholder('-'),
                TextEntry::make('last_name')
                    ->placeholder('-'),
                TextEntry::make('birth_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('company_name')
                    ->placeholder('-'),
                TextEntry::make('ico')
                    ->placeholder('-'),
                TextEntry::make('dic')
                    ->placeholder('-'),
                TextEntry::make('legal_address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('representative_name')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Donor $record): bool => $record->trashed()),
            ]);
    }
}
