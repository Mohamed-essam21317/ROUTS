<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmergencyAlertNotification extends Notification
{
    use Queueable;

    public $alert;

    public function __construct($alert)
    {
        $this->alert = $alert;
    }

    public function via($notifiable)
    {
        return ['mail']; // or ['database', 'mail'] if you want both
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Emergency Alert')
            ->line('An emergency alert has been triggered.')
            ->line('Type: ' . $this->alert->alert_type)
            ->line('From Supervisor ID: ' . $this->alert->user_id)
            ->line('School ID: ' . $this->alert->school_id);
    }
}
