<?php

namespace App\Http\Controllers\client;

use App\Models\PageView;
use Illuminate\Http\Request;

class UserActivityController
{
    // Khi vào trang
    public function start(Request $request)
    {
        $last = PageView::where('session_id', session()->getId())
            ->whereNull('duration')
            ->latest()
            ->first();

        if ($last) {
            // Gửi lại ID cũ để client stop đúng
            return response()->json(['id' => $last->id]);
        }

        $pageView = PageView::create([
            'session_id' => session()->getId(),
            'user_id' => auth()->id(),
            'url' => $request->input('url'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['id' => $pageView->id]);
    }


    // Khi rời trang
    public function stop(Request $request)
    {
        $id = $request->input('id');
        if ($id) {
            $pageView = PageView::find($id);

            if ($pageView && $pageView->created_at) {
                $created = $pageView->created_at;
                $now = now();
                $duration = $created->diffInSeconds($now);

                $pageView->update([
                    'duration' => $duration
                ]);
            }
        }

        return response()->json(['done' => true]);
    }
}
