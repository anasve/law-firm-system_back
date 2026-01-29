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
            ->subject('New Message in Consultation')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have a new message from ' . $senderName)
            ->line('Message: ' . substr($this->message->message, 0, 100) . '...')
            ->action('View Message', url('/consultations/' . $this->message->consultation_id))
            ->line('Thank you.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Message',
            'message' => 'New message in consultation',
            'consultation_id' => $this->message->consultation_id,
            'message_id' => $this->message->id,
        ];
    }
}

