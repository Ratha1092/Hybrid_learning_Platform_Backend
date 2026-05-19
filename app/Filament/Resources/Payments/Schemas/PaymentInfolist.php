<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class PaymentInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Payment Information')
                    ->schema([
                        TextEntry::make('order.order_number')
                            ->label('Order'),
                        TextEntry::make('payment_gateway')
                            ->badge(),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('amount')
                            ->money('USD'),
                        TextEntry::make('currency'),
                        TextEntry::make('transaction_id')
                            ->copyable()
                            ->placeholder('-'),
                        TextEntry::make('external_reference')
                            ->copyable()
                            ->placeholder('-'),
                        TextEntry::make('paid_at')
                            ->dateTime('M d, Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('expires_at')
                            ->dateTime('M d, Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(3),
                Section::make('KHQR')
                    ->schema([
                        TextEntry::make('khqr_payload')
                            ->label('KHQR Payload')
                            ->copyable()
                            ->placeholder('-'),
                    ]),
                Section::make('Gateway Response')
                    ->schema([
                        TextEntry::make('gateway_response')
                            ->label('Response')
                            ->formatStateUsing(fn ($state) =>
                                json_encode(
                                    $state,
                                    JSON_PRETTY_PRINT
                                )
                            )
                            ->placeholder('-'),
                    ]),
            ]);
    }
}