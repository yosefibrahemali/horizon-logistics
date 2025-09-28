<?php

namespace App\Filament\Widgets;

use App\Models\DeliveryMan;
use App\Models\Shipment;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\ChartWidget;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class TopDeliveryMenWidget extends BaseWidget
{
    protected static ?string $heading = 'أفضل السائقين';
    protected int|string|array $columnSpan = 'full';

    public function table(Tables\Table $table): Tables\Table
    {
        $query = DeliveryMan::withCount('shipments')
            ->orderByDesc('shipments_count');

        // إذا لم يكن المستخدم Admin، اقتصر على السائقين المرتبطين بالمستخدم الحالي
        if (Auth::user()->role !== 'admin') {
            $query->where('user_id', Auth::id());
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('name')
                    ->label('اسم السائق')
                    ->sortable(),

                TextColumn::make('shipments_count')
                    ->label('عدد الشحنات')
                    ->sortable()
                    ->badge(),
            ])->emptyStateHeading('لا يوجد أي سائقين');
    }
}
