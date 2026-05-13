<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;

class CartComposer
{
    public function compose(View $view): void
    {
        $cartCount = 0;

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $cartCount = $cart->cartItems()->count(); // Quan hệ phải định nghĩa sẵn trong Cart model
            }
        }

        $view->with('cartCount', $cartCount);
    }
}
