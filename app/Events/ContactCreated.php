<?php
// app/Events/ContactCreated.php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class ContactCreated implements ShouldBroadcastNow
{
    use SerializesModels;

    public $contact;

    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    public function broadcastOn()
    {
        return new Channel('admin-notifications');
    }

    public function broadcastAs()
    {
        return 'contact.submitted';
    }

    public function broadcastWith()
    {
        return [
            'contact_id' => $this->contact->id,
            'name' => $this->contact->first_name . ' ' . $this->contact->last_name,
            'email' => $this->contact->email,
            'message' => $this->contact->message,
            'url' => route('admin.contacts.show', $this->contact->id), // link đến trang quản lý liên hệ
        ];
    }

    public function broadcastVia()
    {
        return ['pusher-notify'];
    }

}