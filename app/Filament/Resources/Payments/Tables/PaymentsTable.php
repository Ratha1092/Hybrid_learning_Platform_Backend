<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Domains\Payments\Enums\PaymentGateway;
use App\Domains\Payments\Enums\PaymentStatus;
use App\Domains\Payments\Services\BakongKhqrService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('payment_gateway')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state) => $state?->value
                    )

                    ->color(fn ($state) => match ($state?->value) {
                        'khqr' => 'warning',
                        'bakong' => 'info',
                        'stripe' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(
                        fn ($state) => $state?->value
                    )

                    ->color(fn ($state) => match ($state?->value) {
                        'paid' => 'success',
                        'completed' => 'success',
                        'pending' => 'warning',
                        'processing' => 'info',
                        'failed' => 'danger',
                        'expired' => 'gray',
                        'refunded' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction')
                    ->limit(20)
                    ->copyable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('last_verified_at')
                    ->label('Verified')
                    ->since()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->since()
                    ->placeholder('-'),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(
                        collect(PaymentStatus::cases())
                            ->mapWithKeys(fn ($status) => [
                                $status->value => str($status->value)->headline(),
                            ])
                            ->toArray()
                    ),
                Tables\Filters\SelectFilter::make('payment_gateway')
                    ->options(
                        collect(PaymentGateway::cases())
                            ->mapWithKeys(fn ($gateway) => [
                                $gateway->value => str($gateway->value)->headline(),])
                            ->toArray()
                    ),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(
                        fn ($record) =>
                            $record->status === PaymentStatus::Pending
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        app(
                            BakongKhqrService::class
                        )->verifyPayment($record);
                    }),
                Action::make('expire')
                    ->label('Expire')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(
                        fn ($record) =>
                            $record->status === PaymentStatus::Pending
                    )
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        app(
                            BakongKhqrService::class
                        )->expirePayment($record);
                    }),
            ])

            ->defaultSort('id', 'desc');
    }
}