<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\User;

class TeacherRegister extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $user;
    public $plainPassword;
    public function __construct(User $user, string $plainPassword)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
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
            ->line('Hello You Are Successfully Registered As User for iSPP System.')
            ->line('Please remember you need to change your password after first login.')
            ->line('Please use the credentials below to log in:')
            ->line('📧 Email: ' . $notifiable->email)
            ->line('🔐 Password: **' . $this->plainPassword . '**')
            ->action('Click here to login into your account for the first time', route('teacher.login'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
