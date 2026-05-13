<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Subscriber;
use App\Mail\ThankYouMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Events\ContactCreated;
use App\Notifications\AdminDatabaseNotification;
use App\Models\User;


class ContactController 
{
    public function index()
    {
        return view('client.contact.index');
    }

    public function store(Request $request)
    {$validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email',
            'phone'      => 'nullable|string',
            'message'    => 'required|string',
        ]);

        // Lưu dữ liệu vào database
        $contact = Contact::create($validated);

        // Gửi email cảm ơn
        Mail::to($contact->email)->send(new ThankYouMail($contact));

        // Nếu khách đồng ý nhận email
        if ($request->has('subscribe') && $request->boolean('subscribe')) {
            // Nếu chưa có email này trong subscribers thì lưu
            Subscriber::firstOrCreate(
                ['email' => $validated['email']],
                ['name' => $validated['first_name'] . ' ' . $validated['last_name']]
            );
        }
        // 🟢 Gửi notification realtime + database cho admin
        event(new ContactCreated($contact));

        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new AdminDatabaseNotification([
                'type' => 'contact_submitted',
                'title' => 'Yêu cầu hỗ trợ mới',
                'message' => $contact->first_name . ' ' . $contact->last_name . ' đã gửi liên hệ: "' . $contact->message . '"',
                'url' => route('admin.contacts.show', $contact->id),
            ]));
        }


        // Trả về redirect nếu là form submit thông thường
        return redirect()->back()->with('success', 'Your message has been sent!');
    }
}
