<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class WaitingTransaction extends BaseWidget
{
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Transaction::whereStatus('waiting')
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('listing.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_per_day')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_days')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fee')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'waiting' => 'warning',
                        'approved' => 'success',
                        'canceled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->actions([
                // Tables\Actions\EditAction::make(),
                Action::make('Approve')
                    ->action(function (Transaction $record) {
                        $record->status = 'approved';
                        $record->save();

                        Notification::make()
                            ->title('Transaction Approved')
                            ->body('The transaction has been approved.')
                            ->success()
                            ->send();
                    })
                    ->button()
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Transaction')
                    ->modalDescription('Are you sure you want to approve this transaction?')
                    ->hidden(fn(Transaction $record) => $record->status !== 'waiting'),
                Action::make('Cancel')
                    ->button()
                    ->color('danger')
                    ->action(function (Transaction $record) {
                        $record->status = 'canceled';
                        $record->save();

                        Notification::make()
                            ->title('Transaction Canceled')
                            ->body('The transaction has been canceled.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Transaction')
                    ->modalDescription('Are you sure you want to cancel this transaction?')
                    ->hidden(fn(Transaction $record) => $record->status !== 'waiting'),
            ]);
    }
}
