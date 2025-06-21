<?php
namespace App\Notifications\Client;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientRejectedNotification extends Notification
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
            ->subject('Your Registration Request was Rejected')
            ->greeting('Hello ' . $this->client->name)
            ->line('We are sorry to inform you that your registration request was rejected.')
            ->line('If you think this is a mistake, please contact support.')
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
            'title'       => 'Registration Request Rejected',
            'message'     => 'Your registration request was rejected. Please contact support.',
            'client_id'   => $this->client->id,
            'client_name' => $this->client->name,
        ];
    }
}
