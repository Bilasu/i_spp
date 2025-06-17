<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentForgotPassword extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    protected $user;
    // public $plainPassword;
    public $token;
    public function __construct($user, $token)
    {
        $this->user = $user;
        // $this->plainPassword = $plainPassword;
        $this->token = $token;
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
            ->subject('Password Reset Requested')
            ->line('Hello, you are successfully registered as a user for the iSPP System.')
            ->line('Please remember you need to change your password after the first login.')
            ->line('Please use the credentials below to log in:')
            ->line('📧 Nama: ' . $this->user->name)
            // ->line('🔐 Password: **' . $this->plainPassword . '**')  // This line displays the temporary password
            ->action('Click here to reset your password', route('student.password.reset', [
                'token' => $this->token,
                'ic' => $notifiable->ic, // Send IC in the reset link
            ]))
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
