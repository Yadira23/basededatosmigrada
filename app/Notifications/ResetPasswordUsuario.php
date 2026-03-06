<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordUsuario extends ResetPassword
{
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->email_usr, // tu columna real
        ], false));

        return (new MailMessage)
            ->subject('Recuperación de contraseña · Sistema Integra')
            ->greeting('Hola ' . ($notifiable->nombre_usr ?? ''))
            ->line('Recibimos una solicitud para restablecer tu contraseña del Sistema Integra.')
            ->action('Restablecer contraseña', $url)
            ->line('Si tú no solicitaste este cambio, puedes ignorar este correo.')
            ->salutation('Atentamente, Equipo del Sistema Integra');
    }
}