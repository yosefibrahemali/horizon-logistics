<?php

use App\Events\WhatsappMessageEvent;
use App\Http\Controllers\DeliveryMenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipmentController;
use App\Notifications\WhatsappApiNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Notification;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->to('user-dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// web.php
Route::get('/shipments/{shipment}/print-label', [ShipmentController::class, 'printLabel'])
    ->name('shipments.print-label');


Route::get('/shipments/{tracking_number}/pay', [ShipmentController::class, 'pay'])->name('shipments.pay');

// Route::get('/delivery/{uuid}', function ($uuid) {
//     // تحقق من التوكن أو الـ UUID
//     $shipments = Shipment::get();

//     return view('filament.pages.delivery-shipments', [
//         'shipments' => $shipments
//     ]);
// })->name('delivery.shipments');

Route::get('/delivery-man/{uuid}', [DeliveryMenController::class, 'show'])->name('deliverymen.show');


Route::get('/test-whatsapp', function () {
    $to = "218913279409"; // رقم المستلم
    $message = "✅ رسالة تجريبية مباشرة من Laravel بدون Event";

    $url = env('WHATSAPP_API_URL') . '/' . env('WHATSAPP_PHONE_NUMBER_ID') . '/messages';

    $response = Http::withToken(env('WHATSAPP_ACCESS_TOKEN'))
        ->post($url, [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'body' => $message,
            ],
        ]);

    // تسجيل الاستجابة في اللوج لمعرفة التفاصيل
    Log::info('WhatsApp API Response:', [
        'status' => $response->status(),
        'body' => $response->body(),
    ]);

    if ($response->successful()) {
        return "✅ تم إرسال رسالة واتساب إلى $to";
    }

    return "❌ فشل الإرسال. كود الحالة: " . $response->status();
});
require __DIR__.'/auth.php';
