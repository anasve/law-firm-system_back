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
            ->subject('تم قبول طلب التوظيف - مرحباً بك!')
            ->greeting('مرحباً ' . $this->name)
            ->line('تم قبول طلب التوظيف بنجاح!')
            ->line('يمكنك الآن تسجيل الدخول باستخدام:')
            ->line('البريد الإلكتروني: ' . $this->email)
            ->line('كلمة المرور المؤقتة: ' . $this->password)
            ->line('⚠️ يرجى تغيير كلمة المرور بعد تسجيل الدخول لأول مرة')
            ->action('تسجيل الدخول', $loginUrl)
            ->line('شكراً لانضمامك إلينا!');
    }
}





