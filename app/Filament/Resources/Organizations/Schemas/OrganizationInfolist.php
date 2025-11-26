<?php

namespace App\Filament\Resources\Organizations\Schemas;

use App\Models\Organization;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrganizationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('street'),
                TextEntry::make('city'),
                TextEntry::make('postal_code'),
                TextEntry::make('country'),
                TextEntry::make('ico'),
                IconEntry::make('vat_payer')
                    ->boolean(),
                TextEntry::make('dic')
                    ->placeholder('-'),
                TextEntry::make('data_box')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address'),
                TextEntry::make('website')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Organization $record): bool => $record->trashed()),
            ]);
    }
}
