<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Domains\Orders\Models\Orders;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use App\Domains\Orders\Enums\OrderStatus;


class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('final_amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->formatStateUsing(fn (OrderPaymentStatus $state) => $state->value)
                    ->color(fn (OrderPaymentStatus $state): string => match ($state) {
                        OrderPaymentStatus::Pending => 'warning',
                        OrderPaymentStatus::Paid => 'success',
                        OrderPaymentStatus::Failed => 'danger',
                        OrderPaymentStatus::Refunded => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => str($state->value)->headline())
                    ->color(fn ($state): string => match ($state->value) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                Tables\Columns\textColumn::make('action')
                    ->label('Action')
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}