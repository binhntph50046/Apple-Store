<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;

class AboutController
{
    public function index()
    {
        return view('client.about.index');
    }
}
