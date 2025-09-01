<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordDeliveryNotification extends Notification
{
    use Queueable;

    protected $password;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($password)
    {
        $this->password = $password;
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
            ->subject('Tu cuenta ha sido activada - Agenda GOB')
            ->greeting('¡Hola ' . $notifiable->username . '!')
            ->line('¡Excelente! Tu correo electrónico ha sido verificado exitosamente y tu cuenta ha sido activada.')
            ->line('Aquí están tus credenciales de acceso:')
            ->line('**Usuario:** ' . $notifiable->username)
            ->line('**Contraseña:** ' . $this->password)
            ->line('Por seguridad, te recomendamos cambiar tu contraseña después del primer inicio de sesión.')
            ->action('Iniciar Sesión', route('login'))
            ->line('¡Bienvenido a Agenda GOB!')
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
        ];
    }
}
