<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class OrderInfolist
{
    public static function configure(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Order Information')
                    ->schema([
                        TextEntry::make('order_number')
                            ->label('Order Number')
                            ->copyable(),
                        TextEntry::make('status')
                            ->badge(),
                        TextEntry::make('payment_status')
                            ->badge(),
                        TextEntry::make('payment_method')
                            ->badge(),
                        TextEntry::make('created_at')
                            ->dateTime('M d, Y H:i'),
                        TextEntry::make('paid_at')
                            ->dateTime('M d, Y H:i')
                            ->placeholder('-'),
                    ])

                    ->columns(3),
                Section::make('Customer')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Customer'),
                        TextEntry::make('customer_email')
                            ->label('Email'),
                        TextEntry::make('user.role')
                            ->badge(),
                    ])
                    ->columns(3),
                Section::make('Payment Summary')

                    ->schema([
                        TextEntry::make('total_amount')
                            ->money('USD'),
                        TextEntry::make('discount_amount')
                            ->money('USD'),
                        TextEntry::make('final_amount')
                            ->money('USD')
                            ->weight('bold')
                            ->color('success'),
                    ])

                    ->columns(3),
                Section::make('Purchased Courses')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->schema([
                                TextEntry::make('course_title')
                                    ->label('Course'),

                                TextEntry::make('instructor.name')
                                    ->label('Instructor'),
                                TextEntry::make('price')
                                    ->money('USD'),
                                TextEntry::make('commission_percentage')
                                    ->suffix('%'),
                                TextEntry::make('instructor_amount')
                                    ->money('USD')
                                    ->color('success'),
                                TextEntry::make('platform_amount')
                                    ->money('USD')
                                    ->color('warning'),
                            ])

                            ->columns(6),
                    ]),
                Section::make('Payments')

                    ->schema([
                        RepeatableEntry::make('payments')
                            ->schema([
                                TextEntry::make('payment_gateway')
                                    ->badge(),
                                TextEntry::make('status')
                                    ->badge(),
                                TextEntry::make('amount')
                                    ->money('USD'),
                                TextEntry::make('transaction_id')
                                    ->copyable()
                                    ->placeholder('-'),
                                TextEntry::make('paid_at')
                                    ->dateTime('M d, Y H:i')
                                    ->placeholder('-'),
                            ])

                            ->columns(5),
                    ]),
            ]);
    }
}