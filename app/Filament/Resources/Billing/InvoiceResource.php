<?php

namespace App\Filament\Resources\Billing;

use App\Domains\Billing\Models\Invoice;
use App\Domains\Billing\Services\InvoiceService;
use App\Filament\Resources\Billing\Pages;
use App\Support\PanelAccess;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class InvoiceResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Invoice::class;
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Invoices';
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 6;
    protected static ?string $recordTitleAttribute = 'invoice_number';

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Number')
                    ->searchable()
                    ->weight('semibold')
                    ->copyable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'invoice',
                        'danger'  => 'credit_note',
                    ])
                    ->formatStateUsing(fn (string $state) => $state === 'credit_note' ? 'Credit Note' : 'Invoice'),

                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order')
                    ->searchable(),

                Tables\Columns\TextColumn::make('order.user.name')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state, Invoice $record) => $record->currency . ' ' . number_format(abs((float) $state), 2))
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state) => $state === 'issued' ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Issued')
                    ->date('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'invoice'     => 'Invoice',
                        'credit_note' => 'Credit Note',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'issued' => 'Issued',
                        'void'   => 'Void',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function (Invoice $record) {
                        if (!$record->pdf_path || !Storage::disk('r2-private')->exists($record->pdf_path)) {
                            app(InvoiceService::class)->regeneratePdf($record);
                            $record->refresh();
                        }
                        return Storage::disk('r2-private')->download(
                            $record->pdf_path,
                            $record->invoice_number . '.pdf',
                            ['Content-Type' => 'application/pdf']
                        );
                    }),

                Tables\Actions\Action::make('resend')
                    ->label('Re-send Email')
                    ->icon('heroicon-o-envelope')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->action(function (Invoice $record) {
                        app(InvoiceService::class)->sendByEmail($record);
                        Notification::make()->title('Email queued for delivery')->success()->send();
                    }),

                Tables\Actions\Action::make('regenerate')
                    ->label('Regenerate PDF')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Invoice $record) {
                        app(InvoiceService::class)->regeneratePdf($record);
                        Notification::make()->title('PDF regenerated')->success()->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return PanelAccess::can('invoices.view');
    }
}
