<?php

namespace App\Notifications\Consultation;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmedNotification extends Notification
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via(object $notifiable): array
    {
        return ['database']; // تعطيل الإيميل مؤقتاً لتجنب حد Mailtrap
    }

    public function toMail(object $notifiable): MailMessage
    {
        $datetime = $this->appointment->datetime->format('Y-m-d H:i');

        return (new MailMessage)
            ->subject('تم تأكيد موعدك')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم تأكيد موعدك بنجاح')
            ->line('التاريخ والوقت: ' . $datetime)
            ->line('المحامي: ' . $this->appointment->lawyer->name)
            ->line('النوع: في المكتب')
            ->line('نتمنى لك استشارة ناجحة');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم تأكيد موعدك',
            'message' => 'تم تأكيد موعدك مع ' . $this->appointment->lawyer->name,
            'appointment_id' => $this->appointment->id,
            'consultation_id' => $this->appointment->consultation_id,
            'datetime' => $this->appointment->datetime->format('Y-m-d H:i'),
        ];
    }

}

