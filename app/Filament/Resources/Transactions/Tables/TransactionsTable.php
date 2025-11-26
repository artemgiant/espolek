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

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bankAccount.id')
                    ->searchable(),
                TextColumn::make('transaction_id')
                    ->searchable(),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('counter_account')
                    ->searchable(),
                TextColumn::make('counter_bank_code')
                    ->searchable(),
                TextColumn::make('counter_bank_name')
                    ->searchable(),
                TextColumn::make('counter_bic')
                    ->searchable(),
                TextColumn::make('counter_account_name')
                    ->searchable(),
                TextColumn::make('ks')
                    ->searchable(),
                TextColumn::make('vs')
                    ->searchable(),
                TextColumn::make('ss')
                    ->searchable(),
                TextColumn::make('user_identification')
                    ->searchable(),
                TextColumn::make('transaction_type')
                    ->searchable(),
                TextColumn::make('executor')
                    ->searchable(),
                TextColumn::make('instruction_id')
                    ->searchable(),
                TextColumn::make('payer_reference')
                    ->searchable(),
                TextColumn::make('operation_type')
                    ->badge(),
                TextColumn::make('expense_type')
                    ->badge(),
                TextColumn::make('income_type')
                    ->badge(),
                TextColumn::make('document_number')
                    ->searchable(),
                TextColumn::make('confirmation_number')
                    ->searchable(),
                TextColumn::make('donor_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('supplier_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('campaign_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                 ,
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ,
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ,
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
