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
            ->subject('استشارة قانونية جديدة')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('لديك استشارة قانونية جديدة من العميل ' . $this->consultation->client->name)
            ->line('الموضوع: ' . $this->consultation->subject)
            ->action('عرض الاستشارة', url('/lawyer/consultations/' . $this->consultation->id))
            ->line('شكراً لك');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'استشارة قانونية جديدة',
            'message' => 'استشارة جديدة من ' . $this->consultation->client->name,
            'consultation_id' => $this->consultation->id,
            'subject' => $this->consultation->subject,
        ];
    }
}

