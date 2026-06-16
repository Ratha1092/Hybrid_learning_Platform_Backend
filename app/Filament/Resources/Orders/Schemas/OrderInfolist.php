<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\FontFamily;

class OrderInfolist
{
    public static function configure(Schema $infolist): Schema
    {
        return $infolist->schema([

            // ── KPI summary row ───────────────────────────────────────────────
            Grid::make(4)->schema([
                Section::make('Order Status')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->schema([
                        TextEntry::make('status')
                            ->label('')
                            ->badge()
                            ->color(fn ($state) => match ($state?->value ?? $state) {
                                'completed' => 'success',
                                'pending'   => 'warning',
                                'cancelled' => 'danger',
                                'refunded'  => 'info',
                                default     => 'gray',
                            }),
                        TextEntry::make('order_number')
                            ->label('Order #')
                            ->copyable()
                            ->icon('heroicon-m-hashtag')
                            ->iconColor('gray')
                            ->weight(FontWeight::SemiBold)
                            ->fontFamily(FontFamily::Mono),
                    ]),

                Section::make('Payment Status')
                    ->icon('heroicon-o-credit-card')
                    ->schema([
                        TextEntry::make('payment_status')
                            ->label('')
                            ->badge()
                            ->color(fn ($state) => match ($state?->value ?? $state) {
                                'paid'       => 'success',
                                'processing' => 'warning',
                                'pending'    => 'warning',
                                'failed'     => 'danger',
                                'expired'    => 'danger',
                                'cancelled'  => 'danger',
                                'refunded'   => 'info',
                                default      => 'gray',
                            }),
                        TextEntry::make('payment_method')
                            ->label('Method')
                            ->badge()
                            ->color('primary'),
                    ]),

                Section::make('Final Amount')
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        TextEntry::make('final_amount')
                            ->label('')
                            ->money('USD')
                            ->weight(FontWeight::Bold)
                            ->color('success'),
                        TextEntry::make('discount_amount')
                            ->label('Discount')
                            ->money('USD')
                            ->color('warning'),
                    ]),

                Section::make('Timing')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->dateTime('M d, Y · H:i')
                            ->icon('heroicon-m-calendar'),
                        TextEntry::make('paid_at')
                            ->label('Paid')
                            ->dateTime('M d, Y · H:i')
                            ->placeholder('Not yet paid')
                            ->icon('heroicon-m-check-circle')
                            ->iconColor('success'),
                    ]),
            ]),

            // ── Customer & payment breakdown ──────────────────────────────────
            Grid::make(2)->schema([
                Section::make('Customer')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Name')
                            ->icon('heroicon-m-user')
                            ->weight(FontWeight::SemiBold),
                        TextEntry::make('customer_email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->copyable(),
                        TextEntry::make('user.role')
                            ->label('Role')
                            ->badge()
                            ->color(fn ($state) => match ($state?->value ?? $state) {
                                'admin'      => 'danger',
                                'instructor' => 'warning',
                                'student'    => 'info',
                                default      => 'gray',
                            }),
                    ])
                    ->columns(3),

                Section::make('Payment Summary')
                    ->icon('heroicon-o-receipt-percent')
                    ->schema([
                        TextEntry::make('total_amount')
                            ->label('Subtotal')
                            ->money('USD')
                            ->icon('heroicon-m-shopping-cart'),
                        TextEntry::make('discount_amount')
                            ->label('Discount')
                            ->money('USD')
                            ->color('warning')
                            ->icon('heroicon-m-tag'),
                        TextEntry::make('final_amount')
                            ->label('Total Charged')
                            ->money('USD')
                            ->weight(FontWeight::Bold)
                            ->color('success')
                            ->icon('heroicon-m-banknotes'),
                    ])
                    ->columns(3),
            ]),

            // ── Purchased courses ─────────────────────────────────────────────
            Section::make('Purchased Courses')
                ->icon('heroicon-o-academic-cap')
                ->schema([
                    RepeatableEntry::make('items')
                        ->label('')
                        ->schema([
                            TextEntry::make('course_title')
                                ->label('Course')
                                ->weight(FontWeight::SemiBold)
                                ->icon('heroicon-m-book-open'),
                            TextEntry::make('instructor.name')
                                ->label('Instructor')
                                ->icon('heroicon-m-user'),
                            TextEntry::make('price')
                                ->label('Price')
                                ->money('USD'),
                            TextEntry::make('commission_percentage')
                                ->label('Commission')
                                ->suffix('%')
                                ->badge()
                                ->color('gray'),
                            TextEntry::make('instructor_amount')
                                ->label('Instructor Payout')
                                ->money('USD')
                                ->weight(FontWeight::Medium)
                                ->color('success'),
                            TextEntry::make('platform_amount')
                                ->label('Platform Revenue')
                                ->money('USD')
                                ->weight(FontWeight::Medium)
                                ->color('warning'),
                        ])
                        ->columns(6),
                ]),

            // ── Payments ──────────────────────────────────────────────────────
            Section::make('Payment History')
                ->icon('heroicon-o-queue-list')
                ->schema([
                    RepeatableEntry::make('payments')
                        ->label('')
                        ->schema([
                            TextEntry::make('payment_gateway')
                                ->label('Gateway')
                                ->badge()
                                ->color('primary'),
                            TextEntry::make('status')
                                ->label('Status')
                                ->badge()
                                ->color(fn ($state) => match ($state?->value ?? $state) {
                                    'paid'       => 'success',
                                    'processing' => 'warning',
                                    'pending'    => 'warning',
                                    'failed'     => 'danger',
                                    'expired'    => 'danger',
                                    default      => 'gray',
                                }),
                            TextEntry::make('amount')
                                ->label('Amount')
                                ->money('USD')
                                ->weight(FontWeight::SemiBold),
                            TextEntry::make('transaction_id')
                                ->label('Transaction ID')
                                ->copyable()
                                ->placeholder('—')
                                ->icon('heroicon-m-identification')
                                ->fontFamily(FontFamily::Mono),
                            TextEntry::make('paid_at')
                                ->label('Paid At')
                                ->dateTime('M d, Y · H:i')
                                ->placeholder('—')
                                ->icon('heroicon-m-calendar'),
                        ])
                        ->columns(5),
                ]),
        ]);
    }
}
