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
            ->subject('تم إلغاء موعدك')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('نأسف لإبلاغك بأن المحامي ' . $this->appointment->lawyer->name . ' ألغى موعدك المحدد في ' . $this->appointment->datetime->format('Y-m-d H:i'))
            ->line('السبب: ' . ($this->appointment->cancellation_reason ?? 'حالة طارئة'))
            ->line('يمكنك حجز موعد جديد من خلال الاستشارة')
            ->action('عرض الاستشارة', url('/consultations/' . $this->appointment->consultation_id))
            ->line('نعتذر عن الإزعاج');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم إلغاء موعدك',
            'message' => 'تم إلغاء موعدك من قبل المحامي ' . $this->appointment->lawyer->name,
            'appointment_id' => $this->appointment->id,
            'consultation_id' => $this->appointment->consultation_id,
            'cancellation_reason' => $this->appointment->cancellation_reason,
            'datetime' => $this->appointment->datetime->format('Y-m-d H:i'),
        ];
    }
}

