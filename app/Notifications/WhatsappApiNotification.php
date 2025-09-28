<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappApiNotification extends Notification
{
    use Queueable;

    protected $to;
    protected $message;

    public function __construct(string $to, string $message)
    {
        $this->to = $to;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database']; // عشان نتجنب خطأ "whatsapp driver not supported"
    }

    public function toDatabase($notifiable)
    {
     //    echo "rr";
        // إرسال للواتساب مباشرة
        $url = env('WHATSAPP_API_URL') . '/' . env('WHATSAPP_PHONE_NUMBER_ID') . '/messages';

       
        $response = Http::withToken(env('WHATSAPP_ACCESS_TOKEN'))
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'to' => $this->to,
                'type' => 'text',
                'text' => ['body' => $this->message],
            ]);

        Log::info("WhatsApp response", [
            'to' => $this->to,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return [
            'to' => $this->to,
            'message' => $this->message,
            'status' => $response->status(),
        ];
    }
}
