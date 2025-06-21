<?php
namespace App\Notifications\Client;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientSuspendedNotification extends Notification
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Account Suspended')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We regret to inform you that your account has been suspended.')
            ->line('If you believe this is a mistake, please contact our support team.')
            ->line('Thank you.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title'       => 'Account Suspended',
            'message'     => 'Your account has been suspended. Please contact support if you think this is an error.',
            'client_id'   => $this->client->id ?? null,
            'client_name' => $this->client->name ?? null,
        ];
    }
}
