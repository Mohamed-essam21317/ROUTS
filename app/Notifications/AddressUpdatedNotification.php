<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class AddressUpdatedNotification extends Notification
{
    use Queueable;

    protected $address;
    protected $message;

    public function __construct($address, $message)
    {
        $this->address = $address;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Address Update Notification')
            ->line($this->message)
            ->line("New Address: " . $this->address->address)
            ->line('Thank you for using Routus.');
    }

    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'title' => 'Address Updated',
            'message' => $this->message,
            'address' => $this->address->address,
        ]);
    }
}
