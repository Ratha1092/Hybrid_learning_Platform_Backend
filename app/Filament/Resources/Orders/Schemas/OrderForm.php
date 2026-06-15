<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Domains\Orders\Enums\OrderPaymentStatus;
use App\Domains\Orders\Enums\OrderStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Order Status')
                ->icon('heroicon-o-arrow-path')
                ->description('Update the order and payment status.')
                ->schema([
                    Select::make('status')
                        ->label('Order Status')
                        ->options([
                            OrderStatus::Pending->value   => 'Pending',
                            OrderStatus::Completed->value => 'Completed',
                            OrderStatus::Cancelled->value => 'Cancelled',
                            OrderStatus::Refunded->value  => 'Refunded',
                        ])
                        ->required(),

                    Select::make('payment_status')
                        ->label('Payment Status')
                        ->options([
                            OrderPaymentStatus::Pending->value    => 'Pending',
                            OrderPaymentStatus::Processing->value => 'Processing',
                            OrderPaymentStatus::Paid->value       => 'Paid',
                            OrderPaymentStatus::Failed->value     => 'Failed',
                            OrderPaymentStatus::Expired->value    => 'Expired',
                            OrderPaymentStatus::Cancelled->value  => 'Cancelled',
                            OrderPaymentStatus::Refunded->value   => 'Refunded',
                        ])
                        ->required(),
                ])
                ->columns(2),

            Section::make('Order Details')
                ->icon('heroicon-o-document-text')
                ->description('Core order information (read-only).')
                ->schema([
                    TextInput::make('order_number')
                        ->label('Order Number')
                        ->disabled(),

                    TextInput::make('total_amount')
                        ->label('Total Amount')
                        ->prefix('$')
                        ->disabled(),

                    TextInput::make('discount_amount')
                        ->label('Discount')
                        ->prefix('$')
                        ->disabled(),

                    TextInput::make('final_amount')
                        ->label('Final Amount')
                        ->prefix('$')
                        ->disabled(),

                    TextInput::make('coupon_code')
                        ->label('Coupon Code')
                        ->disabled(),

                    TextInput::make('payment_method')
                        ->label('Payment Method')
                        ->disabled(),
                ])
                ->columns(3),

            Section::make('Timestamps')
                ->icon('heroicon-o-clock')
                ->schema([
                    DateTimePicker::make('paid_at')
                        ->label('Paid At')
                        ->disabled(),

                    DateTimePicker::make('cancelled_at')
                        ->label('Cancelled At')
                        ->disabled(),

                    DateTimePicker::make('refunded_at')
                        ->label('Refunded At')
                        ->disabled(),
                ])
                ->columns(3),
        ]);
    }
}
