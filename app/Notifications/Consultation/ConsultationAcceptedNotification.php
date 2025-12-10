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
            ->subject('تم قبول استشارتك القانونية')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم قبول استشارتك القانونية من قبل المحامي ' . $this->consultation->lawyer->name)
            ->action('عرض الاستشارة', url('/consultations/' . $this->consultation->id))
            ->line('شكراً لاستخدامك خدماتنا');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم قبول الاستشارة',
            'message' => 'تم قبول استشارتك من قبل المحامي ' . $this->consultation->lawyer->name,
            'consultation_id' => $this->consultation->id,
        ];
    }
}

