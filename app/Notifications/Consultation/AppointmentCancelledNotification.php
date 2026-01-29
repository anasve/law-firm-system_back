<?php

namespace App\Notifications\Consultation;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCancelledNotification extends Notification
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
        return (new MailMessage)
            ->subject('Your Appointment Has Been Cancelled')
            ->greeting('Hello ' . $notifiable->name)
            ->line('We are sorry to inform you that lawyer ' . $this->appointment->lawyer->name . ' has cancelled your appointment scheduled for ' . $this->appointment->datetime->format('Y-m-d H:i'))
            ->line('Reason: ' . ($this->appointment->cancellation_reason ?? 'Emergency'))
            ->line('You can book a new appointment through the consultation.')
            ->action('View Consultation', url('/consultations/' . $this->appointment->consultation_id))
            ->line('We apologize for any inconvenience.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Appointment Cancelled',
            'message' => 'Your appointment was cancelled by lawyer ' . $this->appointment->lawyer->name,
            'appointment_id' => $this->appointment->id,
            'consultation_id' => $this->appointment->consultation_id,
            'cancellation_reason' => $this->appointment->cancellation_reason,
            'datetime' => $this->appointment->datetime->format('Y-m-d H:i'),
        ];
    }
}

