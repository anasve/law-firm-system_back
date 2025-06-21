<?php
namespace App\Notifications\Client;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientApprovedNotification extends Notification
{
    use Queueable;
    protected $client;

    /**
     * Create a new notification instance.
     */
    public function __construct($client)
    {
        $this->client = $client;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Account is Approved')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Good news! Your account has been approved by our staff.')
            ->action('Log in Now', url('/api/client/login'))

            ->line('Thank you for your patience!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'       => 'Account Approved',
            'message'     => 'Your account has been approved and you can now log in.',
            'client_id'   => $this->client ? $this->client->id : null,
            'client_name' => $this->client ? $this->client->name : $notifiable->name,
        ];
    }
}
