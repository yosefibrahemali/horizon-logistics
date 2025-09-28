<?php

namespace App\Filament\Exports;

use App\Models\Shipment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;      // إن أردت تحديد Formats افتراضياً


class ShipmentExporter extends Exporter
{
    protected static ?string $model = Shipment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('sender_id'),
            ExportColumn::make('destination_city'),
            ExportColumn::make('region_id'),
            ExportColumn::make('delivery_man_id'),
            ExportColumn::make('shipment_description'),
            ExportColumn::make('tracking_number'),
            ExportColumn::make('origin_city'),
            ExportColumn::make('receiver_name'),
            ExportColumn::make('receiver_email'),
            ExportColumn::make('receiver_phone'),
            ExportColumn::make('receiver_address'),
            ExportColumn::make('status'),
            ExportColumn::make('payment_method'),
            ExportColumn::make('payment_status'),
            ExportColumn::make('total_weight'),
            ExportColumn::make('shipping_cost'),
            ExportColumn::make('shipment_cost'),
            ExportColumn::make('total_cost'),
            ExportColumn::make('is_fragile'),
            ExportColumn::make('allowed_to_open_and_testing'),
            ExportColumn::make('financial_settlement_status'),
            ExportColumn::make('receive_cost_from'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your shipment export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
    public function getFormats(): array
    {
        return [
            ExportFormat::Xlsx,
            ExportFormat::Csv,
        ];
    }
}
