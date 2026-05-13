<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class AdminContactController
{
    /**
     * Hiển thị danh sách contact
     */
    public function index(Request $request)
    {
        // Query builder với các filter
        $contacts = Contact::query()
            ->when($request->name, function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('first_name', 'like', '%'.$request->name.'%')
                      ->orWhere('last_name', 'like', '%'.$request->name.'%');
                });
            })
            ->when($request->email, function($query) use ($request) {
                $query->where('email', 'like', '%'.$request->email.'%');
            })
            ->when($request->phone, function($query) use ($request) {
                $query->where('phone', 'like', '%'.$request->phone.'%');
            })
            ->when($request->status, function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10);

        return view('admin.contacts.index', compact('contacts'));
    }

    /**
     * Hiển thị chi tiết contact
     */
    public function show(Contact $contact)
    {
        // Đánh dấu là đã đọc nếu status là unread
        if ($contact->status == 'unread') {
            $contact->update(['status' => 'read']);
        }

        return view('admin.contacts.show', compact('contact'));
    }
    public function trash()
    {
        $contacts = Contact::onlyTrashed()->paginate(12);
        return view('admin.contacts.trash', compact('contacts'));
    }
    /**
     * Xóa contact vào thùng rác (soft delete)
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()
            ->route('admin.contacts.index')
            ->with('success', 'Đã xóa liên hệ thành công.');
    }
    public function restore($id)
    {
        $contact = Contact::onlyTrashed()->findOrFail($id);
        $contact->restore();

        return redirect()->route('admin.contacts.trash')->with('success', 'Khôi phục liên hệ thành công!');
    }
    public function forceDelete($id)
    {
        $contact = Contact::onlyTrashed()->findOrFail($id);
        $contact->forceDelete();

        return redirect()->route('admin.contacts.trash')->with('success', 'Xóa liên hệ vĩnh viễn thành công!');
    }
    /**
     * Đánh dấu là đã đọc (dùng cho AJAX nếu cần)
     */
    public function markAsRead(Contact $contact)
    {
        $contact->update(['status' => 'read']);
        
        return response()->json([
            'success' => true,
            'message' => 'Liên hệ đã được đánh dấu là đã đọc'
        ]);
    }

    /**
     * Đánh dấu là đã trả lời (dùng cho AJAX nếu cần)
     */
    public function markAsReplied(Contact $contact)
    {
        $contact->update(['status' => 'replied']);
        
        return response()->json([
            'success' => true,
            'message' => 'Liên hệ đã được đánh dấu là đã trả lời'
        ]);
    }
}