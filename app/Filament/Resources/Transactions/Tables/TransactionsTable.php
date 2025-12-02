<?php

namespace App\Filament\Resources\Transactions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use App\Enums\TransactionCategory;



class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([


                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('operation_type')
                    ->badge(),

                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('counter_account')
                    ->searchable(),
                TextColumn::make('counter_bank_code')
                    ->searchable(),
                TextColumn::make('counter_bank_name')
                    ->searchable(),

                TextColumn::make('counter_account_name')
                    ->searchable(),

                TextColumn::make('vs')
                    ->searchable(),


                TextColumn::make('transaction_type')
                    ->searchable(),
                TextColumn::make('executor')
                    ->searchable(),





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
            ;
    }
}
