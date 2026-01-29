<?php

namespace App\Notifications\Consultation;

use App\Models\Consultation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewConsultationNotification extends Notification
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
            ->subject('New Legal Consultation')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have a new legal consultation from client ' . $this->consultation->client->name)
            ->line('Subject: ' . $this->consultation->subject)
            ->action('View Consultation', url('/lawyer/consultations/' . $this->consultation->id))
            ->line('Thank you.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Legal Consultation',
            'message' => 'New consultation from ' . $this->consultation->client->name,
            'consultation_id' => $this->consultation->id,
            'subject' => $this->consultation->subject,
        ];
    }
}

