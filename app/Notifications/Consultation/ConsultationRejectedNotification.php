<?php

namespace App\Notifications\Consultation;

use App\Models\Consultation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConsultationRejectedNotification extends Notification
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
            ->subject('تم رفض استشارتك القانونية')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('نأسف لإبلاغك بأن استشارتك القانونية تم رفضها من قبل المحامي ' . $this->consultation->lawyer->name)
            ->line('السبب: ' . ($this->consultation->rejection_reason ?? 'لم يتم تحديد سبب'))
            ->line('يمكنك إنشاء استشارة جديدة مع محامي آخر');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم رفض الاستشارة',
            'message' => 'تم رفض استشارتك من قبل المحامي ' . $this->consultation->lawyer->name,
            'consultation_id' => $this->consultation->id,
            'rejection_reason' => $this->consultation->rejection_reason,
        ];
    }
}

