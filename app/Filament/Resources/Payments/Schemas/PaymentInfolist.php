<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;

class PaymentInfolist
{
    public static function configure(Schema $infolist): Schema
    {
        return $infolist->schema([

            // ── Top summary row ───────────────────────────────────────────
            Grid::make(3)->schema([
                Section::make('Amount')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        TextEntry::make('amount')
                            ->label('')
                            ->money('USD')
                            ->weight(\Filament\Support\Enums\FontWeight::Bold)
                            ->color('success'),
                        TextEntry::make('currency')
                            ->label('Currency')
                            ->badge()
                            ->color('gray'),
                    ]),

                Section::make('Status')
                    ->icon('heroicon-o-signal')
                    ->schema([
                        TextEntry::make('status')
                            ->label('')
                            ->badge()
                            ->color(fn ($state) => match (true) {
                                in_array($state?->value ?? $state, ['paid', 'completed']) => 'success',
                                in_array($state?->value ?? $state, ['pending', 'processing']) => 'warning',
                                in_array($state?->value ?? $state, ['failed', 'expired']) => 'danger',
                                ($state?->value ?? $state) === 'refunded' => 'info',
                                default => 'gray',
                            }),
                        TextEntry::make('payment_gateway')
                            ->label('Gateway')
                            ->badge()
                            ->color('primary'),
                    ]),

                Section::make('Timing')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        TextEntry::make('paid_at')
                            ->label('Paid At')
                            ->dateTime('M d, Y · H:i')
                            ->placeholder('Not paid yet')
                            ->icon('heroicon-m-check-circle')
                            ->iconColor('success'),
                        TextEntry::make('expires_at')
                            ->label('Expires At')
                            ->dateTime('M d, Y · H:i')
                            ->placeholder('No expiry')
                            ->icon('heroicon-m-calendar')
                            ->iconColor('warning'),
                    ]),
            ]),

            // ── Order & transaction details ────────────────────────────────
            Grid::make(2)->schema([
                Section::make('Order Details')
                    ->icon('heroicon-o-shopping-bag')
                    ->schema([
                        TextEntry::make('order.order_number')
                            ->label('Order Number')
                            ->copyable()
                            ->icon('heroicon-m-hashtag'),
                        TextEntry::make('order.user.name')
                            ->label('Customer')
                            ->icon('heroicon-m-user')
                            ->placeholder('—'),
                        TextEntry::make('order.total_amount')
                            ->label('Order Total')
                            ->money('USD'),
                        TextEntry::make('order.status')
                            ->label('Order Status')
                            ->badge()
                            ->placeholder('—'),
                    ])
                    ->columns(2),

                Section::make('Transaction References')
                    ->icon('heroicon-o-link')
                    ->schema([
                        TextEntry::make('transaction_id')
                            ->label('Transaction ID')
                            ->copyable()
                            ->placeholder('—')
                            ->icon('heroicon-m-identification'),
                        TextEntry::make('external_reference')
                            ->label('External Reference')
                            ->copyable()
                            ->placeholder('—')
                            ->icon('heroicon-m-arrow-top-right-on-square'),
                        TextEntry::make('idempotency_key')
                            ->label('Idempotency Key')
                            ->copyable()
                            ->placeholder('—')
                            ->icon('heroicon-m-key'),
                        TextEntry::make('payer_account')
                            ->label('Payer Account')
                            ->copyable()
                            ->placeholder('—')
                            ->icon('heroicon-m-credit-card'),
                    ])
                    ->columns(2),
            ]),

            // ── Verification info ──────────────────────────────────────────
            Section::make('Verification')
                ->icon('heroicon-o-shield-check')
                ->collapsed()
                ->schema([
                    TextEntry::make('verification_attempts')
                        ->label('Attempts')
                        ->badge()
                        ->color(fn ($state) => $state > 3 ? 'danger' : 'gray'),
                    TextEntry::make('last_verified_at')
                        ->label('Last Verified')
                        ->dateTime('M d, Y · H:i')
                        ->placeholder('Never'),
                    TextEntry::make('failure_reason')
                        ->label('Failure Reason')
                        ->placeholder('None')
                        ->color('danger')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // ── KHQR payload (only relevant for KHQR/Bakong) ──────────────
            Section::make('KHQR Payload')
                ->icon('heroicon-o-qr-code')
                ->collapsed()
                ->schema([
                    TextEntry::make('khqr_payload')
                        ->label('')
                        ->copyable()
                        ->placeholder('No KHQR payload')
                        ->fontFamily(\Filament\Support\Enums\FontFamily::Mono)
                        ->columnSpanFull(),
                ]),

            // ── Raw gateway response ───────────────────────────────────────
            Section::make('Gateway Response')
                ->icon('heroicon-o-code-bracket')
                ->collapsed()
                ->schema([
                    TextEntry::make('gateway_response')
                        ->label('')
                        ->formatStateUsing(fn ($state) => $state
                            ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                            : null
                        )
                        ->placeholder('No response data')
                        ->fontFamily(\Filament\Support\Enums\FontFamily::Mono)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
