<?php

namespace App\Mail;

use App\Mail\ProductCreated;
use App\Mail\NewProductMail;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Mail;

class SendNewProductEmail
{
    public function handle(ProductCreated $event)
    {
        $subscribers = Subscriber::pluck('email');

        foreach ($subscribers as $email) {
            Mail::to($email)->queue(new NewProductMail($event->product));
        }
    }
}
