<?php

namespace App\Notifications\Consultation;

use App\Models\ConsultationMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMessageNotification extends Notification
{
    use Queueable;

    protected $message;

    public function __construct(ConsultationMessage $message)
    {
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $senderName = $this->message->sender_type === 'client' 
            ? $this->message->consultation->client->name 
            : $this->message->consultation->lawyer->name;

        return (new MailMessage)
            ->subject('رسالة جديدة في الاستشارة')
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('لديك رسالة جديدة من ' . $senderName)
            ->line('الرسالة: ' . substr($this->message->message, 0, 100) . '...')
            ->action('عرض الرسالة', url('/consultations/' . $this->message->consultation_id))
            ->line('شكراً لك');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'رسالة جديدة',
            'message' => 'رسالة جديدة في الاستشارة',
            'consultation_id' => $this->message->consultation_id,
            'message_id' => $this->message->id,
        ];
    }
}

