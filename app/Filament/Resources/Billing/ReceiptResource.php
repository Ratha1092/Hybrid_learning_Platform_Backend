<?php

namespace App\Filament\Resources\Billing;

use App\Domains\Billing\Models\Receipt;
use App\Domains\Billing\Services\ReceiptService;
use App\Filament\Resources\Billing\Pages;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class ReceiptResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Receipt::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationLabel = 'Receipts';
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 7;
    protected static ?string $recordTitleAttribute = 'receipt_number';

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label('Number')
                    ->searchable()
                    ->weight('semibold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order')
                    ->searchable(),

                Tables\Columns\TextColumn::make('order.user.name')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state, Receipt $record) => $record->currency . ' ' . number_format((float) $state, 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_gateway')
                    ->label('Gateway')
                    ->formatStateUsing(fn (string $state) => strtoupper($state)),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Paid At')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('paid_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from'),
                        \Filament\Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'],  fn ($q, $d) => $q->whereDate('paid_at', '>=', $d))
                            ->when($data['until'], fn ($q, $d) => $q->whereDate('paid_at', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function (Receipt $record) {
                        if (!$record->pdf_path || !Storage::disk('local')->exists($record->pdf_path)) {
                            app(ReceiptService::class)->issue($record->order()->with('items')->first());
                            $record->refresh();
                        }
                        return Storage::disk('local')->download(
                            $record->pdf_path,
                            $record->receipt_number . '.pdf',
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                Tables\Actions\Action::make('resend')
                    ->label('Re-send Email')
                    ->icon('heroicon-o-envelope')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->action(function (Receipt $record) {
                        app(ReceiptService::class)->sendByEmail($record);
                        Notification::make()->title('Email queued for delivery')->success()->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReceipts::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return PanelAccess::can('receipts.view');
    }
}
