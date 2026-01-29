<?php

namespace App\Notifications\Consultation;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAppointmentNotification extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $clientName = $this->appointment->client->name;
        $datetime = $this->appointment->datetime->format('Y-m-d H:i');

        return (new MailMessage)
            ->subject('New Appointment')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have a new appointment with client ' . $clientName)
            ->line('Date & Time: ' . $datetime)
            ->line('Type: In Office')
            ->action('View Appointment', url('/appointments/' . $this->appointment->id))
            ->line('Thank you.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Appointment',
            'message' => 'New appointment with ' . $this->appointment->client->name,
            'appointment_id' => $this->appointment->id,
            'consultation_id' => $this->appointment->consultation_id,
            'datetime' => $this->appointment->datetime->format('Y-m-d H:i'),
            'type' => $this->appointment->type,
        ];
    }

}

