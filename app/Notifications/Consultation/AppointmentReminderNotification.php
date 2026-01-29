<?php

namespace App\Notifications\Consultation;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminderNotification extends Notification
{
    use Queueable;

    protected $appointment;
    protected $reminderType; // '24h' or '1h'

    public function __construct(Appointment $appointment, $reminderType = '24h')
    {
        $this->appointment = $appointment;
        $this->reminderType = $reminderType;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // Email disabled temporarily to avoid Mailtrap limit
    }

    public function toMail(object $notifiable): MailMessage
    {
        $datetime = $this->appointment->datetime->format('Y-m-d H:i');
        $timeUntil = $this->reminderType === '24h' ? '24 hours' : '1 hour';

        return (new MailMessage)
            ->subject('Appointment Reminder - ' . $timeUntil)
            ->greeting('Hello ' . $notifiable->name)
            ->line('This is a reminder for your appointment in ' . $timeUntil)
            ->line('Date & Time: ' . $datetime)
            ->line('Type: In Office')
            ->line('We wish you a successful consultation.');
    }

    public function toArray(object $notifiable): array
    {
        $timeUntil = $this->reminderType === '24h' ? '24 hours' : '1 hour';

        return [
            'title' => 'Appointment Reminder',
            'message' => 'Your appointment in ' . $timeUntil . ' - ' . $this->appointment->datetime->format('Y-m-d H:i'),
            'appointment_id' => $this->appointment->id,
            'consultation_id' => $this->appointment->consultation_id,
            'datetime' => $this->appointment->datetime->format('Y-m-d H:i'),
            'reminder_type' => $this->reminderType,
        ];
    }

}

