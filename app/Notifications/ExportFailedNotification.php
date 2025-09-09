<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ExportFailedNotification extends Notification
{
    use Queueable;

    public $errorMessage;
    public $regionId;

    public function __construct(string $errorMessage, int $regionId)
    {
        $this->errorMessage = $errorMessage;
        $this->regionId = $regionId;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Экспорт корпуса предложений завершился ошибкой',
            'error' => $this->errorMessage,
            'region_id' => $this->regionId,
            'time' => now()->toDateTimeString()
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Ошибка экспорта корпуса')
            ->line('При экспорте корпуса предложений произошла ошибка.')
            ->line('Ошибка: ' . $this->errorMessage)
            ->line('Регион ID: ' . $this->regionId)
            ->line('Пожалуйста, обратитесь к администратору.');
    }
}
