<?php

namespace App\Notifications\Admin;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewJobApplicationNotification extends Notification
{
    use Queueable;

    protected $application;

    public function __construct(JobApplication $application)
    {
        $this->application = $application;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('طلب توظيف جديد - ' . ($this->application->type === 'lawyer' ? 'محامي' : 'موظف'))
            ->greeting('مرحباً ' . $notifiable->name)
            ->line('تم استلام طلب توظيف جديد:')
            ->line('الاسم: ' . $this->application->name)
            ->line('البريد الإلكتروني: ' . $this->application->email)
            ->line('النوع: ' . ($this->application->type === 'lawyer' ? 'محامي' : 'موظف'))
            ->line('رقم الهاتف: ' . ($this->application->phone ?? 'غير متوفر'))
            ->action('عرض الطلب', url('/admin/job-applications/' . $this->application->id))
            ->line('شكراً لك');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'طلب توظيف جديد',
            'message' => 'طلب توظيف جديد من ' . $this->application->name . ' (' . ($this->application->type === 'lawyer' ? 'محامي' : 'موظف') . ')',
            'application_id' => $this->application->id,
            'application_type' => $this->application->type,
            'applicant_name' => $this->application->name,
        ];
    }
}





