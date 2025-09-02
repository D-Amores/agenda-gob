<?php

namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPasswordNotification extends Notification
{
    use Queueable;

    protected $newPassword;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($newPassword)
    {
        $this->newPassword = $newPassword;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nueva contraseña - Agenda GOB')
            ->greeting('¡Hola ' . $notifiable->username . '!')
            ->line('Hemos recibido una solicitud para restablecer tu contraseña.')
            ->line('Tu nueva contraseña es:')
            ->line('**' . $this->newPassword . '**')
            ->line('Por seguridad, te recomendamos cambiar esta contraseña después de iniciar sesión.')
            ->action('Iniciar Sesión', route('login'))
            ->line('Si no solicitaste este cambio, por favor contacta al administrador inmediatamente.')
            ->line('¡Gracias por usar Agenda GOB!')
            ->salutation('Saludos,')
            ->salutation('El equipo de Agenda GOB');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'user_id' => $notifiable->id,
            'username' => $notifiable->username,
            'email' => $notifiable->email,
            'action' => 'password_reset'
        ];
    }
}
