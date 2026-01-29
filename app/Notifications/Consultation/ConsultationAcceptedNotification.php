<?php

namespace App\Notifications\Consultation;

use App\Models\Consultation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConsultationAcceptedNotification extends Notification
{
    use Queueable;

    protected $consultation;

    public function __construct(Consultation $consultation)
    {
        $this->consultation = $consultation;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Legal Consultation Has Been Accepted')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your legal consultation has been accepted by lawyer ' . $this->consultation->lawyer->name)
            ->action('View Consultation', url('/consultations/' . $this->consultation->id))
            ->line('Thank you for using our services.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Consultation Accepted',
            'message' => 'Your consultation was accepted by lawyer ' . $this->consultation->lawyer->name,
            'consultation_id' => $this->consultation->id,
        ];
    }
}

