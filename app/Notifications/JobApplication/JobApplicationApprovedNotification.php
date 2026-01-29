<?php

namespace App\Notifications\JobApplication;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobApplicationApprovedNotification extends Notification
{
    use Queueable;

    protected $userType;
    protected $email;
    protected $password;
    protected $name;

    public function __construct($userType, $email, $password, $name)
    {
        $this->userType = $userType;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $loginUrl = $this->userType === 'lawyer' 
            ? url('/lawyer/login')
            : url('/employee/login');

        return (new MailMessage)
            ->subject('Job Application Approved - Welcome!')
            ->greeting('Hello ' . $this->name)
            ->line('Your job application has been approved!')
            ->line('You can now sign in using:')
            ->line('Email: ' . $this->email)
            ->line('Temporary password: ' . $this->password)
            ->line('⚠️ Please change your password after first login.')
            ->action('Sign In', $loginUrl)
            ->line('Thank you for joining us!');
    }
}





