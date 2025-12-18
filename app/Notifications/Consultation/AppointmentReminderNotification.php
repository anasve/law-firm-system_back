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
        return ['database']; // تعطيل الإيميل مؤقتاً لتجنب حد Mailtrap
    }

    public function toMail(object $notifiable): MailMessage
    {
        $datetime = $this->appointment->datetime->format('Y-m-d H:i');
        $timeUntil = $this->reminderType === '24h' ? '24 ساعة' : 'ساعة واحدة';

        return (new MailMessage)
            ->subject('تذكير بالموعد - ' . $timeUntil)
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('هذا تذكير بموعدك المحدد بعد ' . $timeUntil)
            ->line('التاريخ والوقت: ' . $datetime)
            ->line('النوع: في المكتب')
            ->line('نتمنى لك استشارة ناجحة');
    }

    public function toArray(object $notifiable): array
    {
        $timeUntil = $this->reminderType === '24h' ? '24 ساعة' : 'ساعة واحدة';

        return [
            'title' => 'تذكير بالموعد',
            'message' => 'موعدك بعد ' . $timeUntil . ' - ' . $this->appointment->datetime->format('Y-m-d H:i'),
            'appointment_id' => $this->appointment->id,
            'consultation_id' => $this->appointment->consultation_id,
            'datetime' => $this->appointment->datetime->format('Y-m-d H:i'),
            'reminder_type' => $this->reminderType,
        ];
    }

}

