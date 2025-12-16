<?php
namespace App\Notifications\Employee;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewClientRegistrationNotification extends Notification
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
        // Temporarily disable email to avoid Mailtrap rate limit
        // Only use database notifications for now
        // To enable email: change to ['mail', 'database'] and use queue with delay
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Client Registration Request')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new client has just registered and is waiting for approval.')
            ->line('Client Name: ' . $this->client->name)
            ->line('Email: ' . $this->client->email)
            ->line('Phone: ' . ($this->client->phone ?? 'N/A'))
            ->line('Please review and activate their account if appropriate.')
            ->action('View Clients', url('/dashboard/employees/clients')) // adjust URL as needed
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
            'message'      => 'New client registered: ' . $this->client->name,
            'client_id'    => $this->client->id,
            'client_name'  => $this->client->name,
            'client_email' => $this->client->email,
            'client_phone' => $this->client->phone,
        ];
    }
}
