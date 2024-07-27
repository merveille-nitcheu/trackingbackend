<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordNotification extends Notification
{
    use Queueable;
    protected $user;
    protected $password;

    /**
     * Create a new notification instance.
     */
    public function __construct(mixed $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
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
        $d = [
            'name' => $this->user->name,
            'password' => $this->password,
            'email' => $this->user->email,
            'link' => config('app.link_login_page')
        ];
        return (new MailMessage)
                    ->subject('Tracking by Light Group: Login information')
                    ->mailer('smtp')
                    ->view('LoginInfoToUserView', $d);

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
