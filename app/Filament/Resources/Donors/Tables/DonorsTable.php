<?php

namespace App\Filament\Resources\Donors\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DonorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'individual' => 'Фізична',
                        'company' => 'Юридична',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'individual' => 'info',
                        'company' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('display_name')
                    ->label('Ім\'я / Назва')
                    ->searchable(['first_name', 'last_name', 'company_name'])
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ,
                TextColumn::make('phone')
                    ->label('Телефон')
                   ,
                TextColumn::make('ico')
                    ->label('IČO')
                    ->searchable()

                    ->placeholder('-'),
                TextColumn::make('transactions_count')
                    ->label('Транзакцій')
                    ->counts('transactions')
                    ->badge()
                    ->color('success'),
                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime()
                    ->sortable()
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Тип')
                    ->options([
                        'individual' => 'Фізична особа',
                        'company' => 'Юридична особа',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->searchable(false)
            ->defaultSort('created_at', 'desc');
    }
}
