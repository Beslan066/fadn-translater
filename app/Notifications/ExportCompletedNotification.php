<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;

class ExportCompletedNotification extends Notification
{
    use Queueable;

    public $fileName;
    public $regionId;
    public $exportId;

    public function __construct(string $fileName, int $regionId, int $exportId)
    {
        $this->fileName = $fileName;
        $this->regionId = $regionId;
        $this->exportId = $exportId;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Экспорт корпуса предложений завершен',
            'file_name' => $this->fileName,
            'region_id' => $this->regionId,
            'export_id' => $this->exportId,
            'download_url' => route('export.download', ['fileName' => $this->file_name]),

            'time' => now()->toDateTimeString()
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Экспорт корпуса завершен')
            ->line('Экспорт корпуса предложений для региона завершен.')
            ->line('Файл: ' . $this->fileName)
            ->action('Скачать файл', route('export.download', $this->fileName))
            ->line('Спасибо за использование нашего приложения!');
    }
}
