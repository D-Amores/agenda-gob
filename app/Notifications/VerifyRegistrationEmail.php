<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PendingRegistration;

class VerifyRegistrationEmail extends Notification
{
    use Queueable;

    protected $pendingRegistration;
    protected $verificationUrl;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(PendingRegistration $pendingRegistration, $verificationUrl)
    {
        $this->pendingRegistration = $pendingRegistration;
        $this->verificationUrl = $verificationUrl;
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
            ->subject('Verificar tu correo electrónico - Agenda GOB')
            ->greeting('¡Hola ' . $this->pendingRegistration->username . '!')
            ->line('Hemos recibido una solicitud de registro para tu cuenta en Agenda GOB.')
            ->line('Para completar tu registro, por favor verifica tu correo electrónico haciendo clic en el botón de abajo:')
            ->action('Verificar correo electrónico', $this->verificationUrl)
            ->line('Este enlace expirará en 24 horas por seguridad.')
            ->line('Si no solicitaste este registro, puedes ignorar este correo.')
            ->line('¡Gracias por unirte a Agenda GOB!')
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
            'pending_registration_id' => $this->pendingRegistration->id,
            'email' => $this->pendingRegistration->email,
            'username' => $this->pendingRegistration->username,
        ];
    }
}
