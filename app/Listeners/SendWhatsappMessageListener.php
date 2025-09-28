<?php

namespace App\Listeners;

use App\Events\WhatsappMessageEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\WhatsappApiNotification;
use Illuminate\Support\Facades\Notification;

class SendWhatsappMessageListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WhatsappMessageEvent $event): void
    {
      //  dd();
        Notification::route('whatsapp', $event->to)
            ->notify(new WhatsappApiNotification($event->to, $event->message));
    }
}
