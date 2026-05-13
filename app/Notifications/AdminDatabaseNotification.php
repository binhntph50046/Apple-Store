<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminDatabaseNotification extends Notification
{
    use Queueable;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    // Nội dung lưu vào database
    public function toDatabase($notifiable)
    {
        return [
            'type' => $this->data['type'],
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'user_id' => $this->data['user_id'] ?? null,
            'user_name' => $this->data['user_name'] ?? null,
            'url' => $this->data['url'] ?? null,
            'order_id' => $this->data['order_id'] ?? null,
        ];
    }
}
