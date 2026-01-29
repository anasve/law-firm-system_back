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
            ->subject('New Job Application - ' . ($this->application->type === 'lawyer' ? 'Lawyer' : 'Employee'))
            ->greeting('Hello ' . $notifiable->name)
            ->line('A new job application has been received:')
            ->line('Name: ' . $this->application->name)
            ->line('Email: ' . $this->application->email)
            ->line('Type: ' . ($this->application->type === 'lawyer' ? 'Lawyer' : 'Employee'))
            ->line('Phone: ' . ($this->application->phone ?? 'N/A'))
            ->action('View Application', url('/admin/job-applications/' . $this->application->id))
            ->line('Thank you.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Job Application',
            'message' => 'New job application from ' . $this->application->name . ' (' . ($this->application->type === 'lawyer' ? 'Lawyer' : 'Employee') . ')',
            'application_id' => $this->application->id,
            'application_type' => $this->application->type,
            'applicant_name' => $this->application->name,
        ];
    }
}





