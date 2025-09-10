<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordGenerated extends Notification
{
    use Queueable;

    protected $password;

    /**
     * Create a new notification instance.
     */
    public function __construct($password)
    {
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tu contraseña de acceso - Agenda Gubernamental')
            ->greeting('¡Hola ' . $notifiable->username . '!')
            ->line('¡Bienvenido a la Agenda Gubernamental! Tu email ha sido verificado exitosamente.')
            ->line('Hemos generado una contraseña segura para tu cuenta:')
            ->line('**Contraseña:** ' . $this->password)
            ->line('**Usuario:** ' . $notifiable->username)
            ->action('Iniciar Sesión', url('/login'))
            ->line('Por tu seguridad, te recomendamos cambiar esta contraseña después de iniciar sesión.')
            ->line('Si no creaste esta cuenta, puedes ignorar este mensaje.')
            ->salutation('Saludos,<br>El equipo de Agenda Gob');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'password_sent' => true,
            'user_id' => $notifiable->id,
        ];
    }
}
