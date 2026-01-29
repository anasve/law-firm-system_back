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
            ->subject('Your Legal Consultation Has Been Rejected')
            ->greeting('Hello ' . $notifiable->name)
            ->line('We are sorry to inform you that your legal consultation was rejected by lawyer ' . $this->consultation->lawyer->name)
            ->line('Reason: ' . ($this->consultation->rejection_reason ?? 'No reason specified'))
            ->line('You can create a new consultation with another lawyer.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Consultation Rejected',
            'message' => 'Your consultation was rejected by lawyer ' . $this->consultation->lawyer->name,
            'consultation_id' => $this->consultation->id,
            'rejection_reason' => $this->consultation->rejection_reason,
        ];
    }
}

