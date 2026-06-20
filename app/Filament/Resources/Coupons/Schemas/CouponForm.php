<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Domains\Promotions\Models\Coupon;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('code')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(64)
                                    ->formatStateUsing(fn (?string $state) => $state ? strtoupper($state) : $state)
                                    ->dehydrateStateUsing(fn (?string $state) => $state ? strtoupper(trim($state)) : $state)
                                    ->helperText('Shoppers enter this at checkout. Always stored uppercase.'),
                                Toggle::make('is_active')
                                    ->label('Active')
                                    ->default(true)
                                    ->helperText('Inactive coupons cannot be redeemed.'),
                            ]),
                        Textarea::make('description')
                            ->maxLength(500)
                            ->rows(2)
                            ->helperText('Internal note — not shown to customers.'),
                    ]),

                Section::make('Discount')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->options([
                                        Coupon::TYPE_PERCENTAGE => 'Percentage off',
                                        Coupon::TYPE_FIXED => 'Fixed amount off',
                                    ])
                                    ->default(Coupon::TYPE_PERCENTAGE)
                                    ->required()
                                    ->live(),
                                TextInput::make('value')
                                    ->label(fn (callable $get) => $get('type') === Coupon::TYPE_FIXED ? 'Amount off ($)' : 'Percentage off (%)')
                                    ->numeric()
                                    ->required()
                                    ->minValue(0.01)
                                    ->maxValue(fn (callable $get) => $get('type') === Coupon::TYPE_PERCENTAGE ? 100 : 999999),
                                TextInput::make('min_order_amount')
                                    ->label('Minimum order amount ($)')
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText('Leave blank for no minimum.'),
                            ]),
                    ]),

                Section::make('Usage limits')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('max_uses')
                                    ->label('Total redemption limit')
                                    ->numeric()
                                    ->minValue(1)
                                    ->helperText('Leave blank for unlimited total uses.'),
                                TextInput::make('max_uses_per_user')
                                    ->label('Uses per customer')
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->minValue(1),
                            ]),
                    ]),

                Section::make('Schedule')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                DateTimePicker::make('starts_at')
                                    ->label('Starts at')
                                    ->helperText('Leave blank to start immediately.'),
                                DateTimePicker::make('expires_at')
                                    ->label('Expires at')
                                    ->helperText('Leave blank for no expiry.'),
                            ]),
                    ]),
            ]);
    }
}
