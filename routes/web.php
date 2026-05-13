<?php

use App\Models\User;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Models
use App\Http\Middleware\VerifyCsrfToken;

// Admin Controllers
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\admin\FaqController;
use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\admin\BlogController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\OrderController;
use App\Http\Controllers\Admin\RobotController;
use App\Http\Controllers\auth\GoogleController;
use App\Http\Controllers\client\CartController;
use App\Http\Controllers\client\ChatController;
use App\Http\Controllers\client\FaqsController;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\client\ShopController;
use App\Http\Controllers\admin\BannerController;
use App\Http\Controllers\Admin\NotifyController;
use App\Http\Controllers\client\AboutController;
use App\Notifications\AdminDatabaseNotification;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\Admin\SitemapController;
use App\Http\Controllers\admin\VoucherController;
use App\Http\Controllers\auth\FacebookController;
use App\Http\Controllers\client\SearchController;
use App\Http\Controllers\admin\ActivityController;
use App\Http\Controllers\admin\CategoryController;
// Client Controllers
use App\Http\Controllers\client\ChatBotController;
use App\Http\Controllers\client\CompareController;
use App\Http\Controllers\client\ContactController;
use App\Http\Controllers\client\PaymentController;
use App\Http\Controllers\client\ProfileController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\FlashSaleController;
use App\Http\Controllers\Admin\MessengerController;
use App\Http\Controllers\admin\SubcriberController;
use App\Http\Controllers\client\CheckoutController;
use App\Http\Controllers\client\WishlistController;
use App\Http\Controllers\client\SubscribeController;
use App\Http\Controllers\admin\AdminContactController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\auth\ResetPasswordController;
use App\Http\Controllers\admin\FlashSaleItemController;
use App\Http\Controllers\admin\SpecificationController;
use App\Http\Controllers\auth\ForgotPasswordController;
use App\Http\Controllers\client\NotificationController;
use App\Http\Controllers\client\UserActivityController;
use App\Http\Controllers\admin\ProductVariantController;

// Auth Controllers
use App\Http\Controllers\client\ProductReviewController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\admin\VariantAttributeTypeController;
use App\Http\Controllers\client\BlogController as ClientBlogController;
use App\Http\Controllers\client\OrderController as ClientOrderController;
use App\Http\Controllers\client\ProductController as ClientProductController;
use App\Http\Controllers\client\VoucherController as ClientVoucherController;
use App\Http\Controllers\Admin\OrderReturnController as AdminOrderReturnController;
use App\Http\Controllers\client\OrderReturnController as ClientOrderReturnController;
use App\Http\Controllers\admin\ProductReviewController as AdminProductReviewController;

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
*/

Route::post('/track/start', [UserActivityController::class, 'start']);
Route::post('/track/stop', [UserActivityController::class, 'stop']);

// Home & Product Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{slug}', [ClientProductController::class, 'show'])->name('product.detail');
Route::get('/api/products/{id}', [ClientProductController::class, 'getProductDetails'])->name('api.products.show');
Route::get('/api/variants/{id}', [ClientProductController::class, 'getVariant'])->name('api.variants.show');
Route::post('/increment-view/{id}', [HomeController::class, 'incrementView'])->name('increment.view');
Route::get('/compare', [CompareController::class, 'index'])->name('compare.index');

// Shop Routes
Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/shop/{slug}', [ShopController::class, 'showCategory'])->name('shop.category');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/shop/filter-data', [App\Http\Controllers\client\ShopController::class, 'getFilterData'])->name('shop.filter-data');

// Profile Routes
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::get('/password', [ProfileController::class, 'password'])->name('password');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
    Route::get('/orders', [ProfileController::class, 'orders'])->name('orders');
});

// Blog Routes
Route::get('/blog', [ClientBlogController::class, 'index'])->name('blog');
Route::get('/blog/{slug}', [ClientBlogController::class, 'show'])->name('blog.show');

// Contact Routes
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/faq', [FaqsController::class, 'index'])->name('faq');

//Location
Route::get('/location', function () {
    return view('client.location.index');
})->name('location');

Route::post('/chatbot/ask', [ChatBotController::class, 'ask'])->name('chatbot.ask');

// Cart & Checkout Routes
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::put('/cart/update/{cartItemId}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/update-quantity', [CartController::class, 'updateQuantity'])->name('cart.updateQuantity');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart', [CartController::class, 'index'])->name('cart');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
// Cart Checkout Routes
Route::get('/cart/checkout', [CheckoutController::class, 'cartCheckout'])->name('cart.checkout');
Route::post('/cart/checkout', [CheckoutController::class, 'processCartCheckout'])->name('cart.checkout.store');
Route::post('/cart/checkout/vnpay', [PaymentController::class, 'cartVnPay'])->name('cart.checkout.vnpay');

Route::match(['get', 'post'], '/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/checkout/vnpay/callback', [CheckoutController::class, 'vnpayCallback'])->name('checkout.vnpay.callback');

// Payment Routes
Route::post('/payment/vnpay', [PaymentController::class, 'vnPay'])->name('vnpay.payment');
Route::get('/payment/vnpay/return', [PaymentController::class, 'vnPayReturn'])->name('vnpay.return');

// Wishlist Routes (Authenticated)
Route::middleware(['auth'])->group(function () {
    Route::post('wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
});

// Subscribe Route
Route::post('/subscribe', [SubscribeController::class, 'store'])->name('subscribe.store');

// Route cho khách gửi yêu cầu hoàn hàng và theo dõi đơn hàng
Route::middleware(['auth'])->group(function () {
    Route::prefix('order')->name('order.')->group(function () {
        // Danh sách và theo dõi đơn hàng
        Route::get('/', [ClientOrderController::class, 'index'])->name('index');
        Route::get('/tracking/{order}', [CheckoutController::class, 'tracking'])->name('tracking');
        Route::post('/cancel/{order}', [ClientOrderController::class, 'cancel'])->name('cancel');
        Route::post('/confirm-received/{order}', [ClientOrderController::class, 'confirmReceived'])->name('confirm-received');
        Route::get('/invoice/{order}', [CheckoutController::class, 'invoice'])->name('invoice');
        Route::get('/resend-invoice/{order}', [CheckoutController::class, 'resendInvoice'])->name('resend-invoice');
        Route::post('{id}/request-resend-invoice', [ClientOrderController::class, 'requestResendInvoice'])->name('request-resend-invoice');

        // Hoàn đơn
        Route::prefix('return')->name('returns.')->group(function () {
            Route::get('/{order}', [ClientOrderReturnController::class, 'create'])->name('create');
            Route::post('/{order}', [ClientOrderReturnController::class, 'store'])->name('store');
            Route::get('/{order}/{return}', [ClientOrderReturnController::class, 'show'])->name('show');
        });
    });
});

// Guest tracking
Route::get('/order/guest-tracking/{order_code?}', [ClientOrderController::class, 'guestTracking'])->name('order.guest.tracking');

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|staff'])->group(function () {
    Route::get('order-returns', [AdminOrderReturnController::class, 'index'])->name('order-returns.index');
    Route::get('order-returns/{id}', [AdminOrderReturnController::class, 'show'])->name('order-returns.show');
    Route::post('order-returns/{id}/approve', [AdminOrderReturnController::class, 'approve'])->name('order-returns.approve');
    Route::post('order-returns/{id}/reject', [AdminOrderReturnController::class, 'reject'])->name('order-returns.reject');
});

// Route cho khách gửi yêu cầu hoàn hàng
Route::middleware(['auth'])->group(function () {
    Route::get('order/{order}/return', [ClientOrderReturnController::class, 'create'])->name('order.returns.create');
    Route::post('order/{order}/return', [ClientOrderReturnController::class, 'store'])->name('order.returns.store');
});

// Theo dõi đơn hàng sau khi đặt hàng
Route::get('/order', [ClientOrderController::class, 'index'])->name('order.index');
Route::post('/order/cancel/{order}', [ClientOrderController::class, 'cancel'])->name('order.cancel');
Route::get('/order/tracking/{order}', [CheckoutController::class, 'tracking'])->name('order.tracking');
Route::get('/order/invoice/{order}', [CheckoutController::class, 'invoice'])->name('order.invoice');
Route::get('/order/resend-invoice/{order}', [CheckoutController::class, 'resendInvoice'])->name('order.resend-invoice');

Route::prefix('order')->name('order.')->group(function () {
    Route::post('{id}/request-resend-invoice', [ClientOrderController::class, 'requestResendInvoice'])->name('request-resend-invoice');
});

// Chatify Messenger Client
Route::get('/chat', [ChatController::class, 'index'])->name('client.chat');
// Route::get('/chat/unread-count', [ChatController::class, 'unreadCount']);

// Product Review

// Route 1: Xem lịch sử đánh giá 1 biến thể
Route::get('{order}/review/{variant}/history', [ProductReviewController::class, 'history'])->name('order.review.history');
Route::get('order/{order}/review', [ProductReviewController::class, 'create'])->name('order.review');
Route::post('order/{order}/review/{variant}', [ProductReviewController::class, 'store'])->name('order.review.store');
// Route 2: Xem lịch sử đánh giá toàn bộ đơn hàng
Route::get('order/{order}/review/history', [ProductReviewController::class, 'historyAll'])->name('order.review.history.all');
/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// Social Login Routes
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
Route::get('/auth/facebook', [FacebookController::class, 'redirectToFacebook'])->name('auth.facebook.redirect');
Route::get('/auth/facebook/callback', [FacebookController::class, 'handleFacebookCallback']);

Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
});

// Trang nhắc xác minh
Route::get('/email/verify', function () {
    return view('auth.verify-email'); // bạn cần tạo view này
})->middleware('auth')->name('verification.notice');

// Khi user click link xác minh trong email
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = User::findOrFail($id);

    // Nếu chưa xác minh thì cập nhật
    if (is_null($user->email_verified_at)) {
        $user->email_verified_at = now();
        $user->save();
    }

    // (Tuỳ chọn) đăng nhập luôn user sau khi xác minh
    Auth::login($user);

    return redirect('/')->with('success', 'Xác minh email thành công!');
})->middleware(['signed'])->name('verification.verify');

// Gửi lại email xác minh
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Email xác minh đã được gửi!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//Notification
Route::post('/notifications/read/{id}', function ($id) {
    $noti = auth()->user()->unreadNotifications()->findOrFail($id);
    $noti->markAsRead();
    return response()->json(['success' => true]);
});
Route::post('/notifications/read-all', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return response()->json(['success' => true]);
});

Route::middleware(['auth'])->group(function () {
    // ... other auth routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/show/{id}', [NotificationController::class, 'show'])->name('notifications.show');
});


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth', 'role:admin|staff'])
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Live Chat Management
        Route::prefix('livechat')->name('livechat.')->group(function () {
            Route::get('/', [MessengerController::class, 'index'])->name('index');
            Route::get('/fetch', [MessengerController::class, 'fetch'])->name('fetch');
            Route::get('/users', [MessengerController::class, 'getUsers'])->name('users');
            Route::get('/messages/{userId}', [MessengerController::class, 'getMessages'])->name('messages');
            Route::post('/send', [MessengerController::class, 'sendMessage'])->name('send');
        });

        //Notification
        Route::prefix('notify')->name('notify.')->group(function () {
            Route::get('/', [NotifyController::class, 'index'])->name('index');
            Route::delete('/{id}', [NotifyController::class, 'destroy'])->name('destroy');
            Route::get('/trash', [NotifyController::class, 'trash'])->name('trash');
            Route::post('/restore/{id}', [NotifyController::class, 'restore'])->name('restore');
            Route::delete('/force-delete/{id}', [NotifyController::class, 'forceDelete'])->name('forceDelete');
            Route::post('/mark-as-read/{id}', [NotifyController::class, 'markAsRead'])->name('markAsRead');
        });

        // User Management
        Route::prefix('users')->name('users.')->middleware('permission:view users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->middleware('permission:create users')->name('create');
            Route::post('/', [UserController::class, 'store'])->middleware('permission:create users')->name('store');
            Route::get('/trash', [UserController::class, 'trash'])->name('trash');
            Route::get('/ban-reasons', [UserController::class, 'banReasons'])->name('ban-reasons');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->middleware('permission:edit users')->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:edit users')->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:delete users')->name('destroy');
            Route::post('/{user}/restore', [UserController::class, 'restore'])->middleware('permission:edit users')->name('restore');
            Route::delete('/{user}/force-delete', [UserController::class, 'forceDelete'])->middleware('permission:delete users')->name('forceDelete');
            Route::post('/toggle-status/{user}', [UserController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Category Management
        Route::prefix('categories')->name('categories.')->middleware('permission:view categories')->group(function () {
            Route::get('/trash', [CategoryController::class, 'trash'])->name('trash');
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->middleware('permission:create categories')->name('create');
            Route::post('/', [CategoryController::class, 'store'])->middleware('permission:create categories')->name('store');
            Route::get('/trash', [CategoryController::class, 'trash'])->name('trash');
            Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
            Route::get('/{category}/edit', [CategoryController::class, 'edit'])->middleware('permission:edit categories')->name('edit');
            Route::put('/{category}', [CategoryController::class, 'update'])->middleware('permission:edit categories')->name('update');
            Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('permission:delete categories')->name('destroy');
            Route::post('/{category}/restore', [CategoryController::class, 'restore'])->middleware('permission:edit categories')->name('restore');
            Route::delete('/{category}/force-delete', [CategoryController::class, 'forceDelete'])->middleware('permission:delete categories')->name('forceDelete');
            Route::post('/change-order', [CategoryController::class, 'changeOrder'])->middleware('permission:edit categories')->name('changeOrder');
            Route::get('/{category}/specifications', [CategoryController::class, 'getSpecifications'])->name('specifications');
            Route::get('/{category}/attributes', [CategoryController::class, 'getAttributes'])->name('attributes');
        });

        // Banner Management
        Route::prefix('banners')->name('banners.')->middleware('permission:view banners')->group(function () {
            Route::get('/', [BannerController::class, 'index'])->name('index');
            Route::get('/create', [BannerController::class, 'create'])->middleware('permission:create banners')->name('create');
            Route::post('/', [BannerController::class, 'store'])->middleware('permission:create banners')->name('store');
            Route::get('/{banner}', [BannerController::class, 'show'])->name('show');
            Route::get('/{banner}/edit', [BannerController::class, 'edit'])->middleware('permission:edit banners')->name('edit');
            Route::put('/{banner}', [BannerController::class, 'update'])->middleware('permission:edit banners')->name('update');
            Route::delete('/{banner}', [BannerController::class, 'destroy'])->middleware('permission:delete banners')->name('destroy');
            Route::post('/{banner}/move-up', [BannerController::class, 'moveUp'])->middleware('permission:edit banners')->name('moveUp');
            Route::post('/{banner}/move-down', [BannerController::class, 'moveDown'])->middleware('permission:edit banners')->name('moveDown');
        });

        // Flash Sale Management - Staff chỉ có quyền xem
        Route::prefix('flash-sales')->name('flash-sales.')->middleware('permission:view orders')->group(function () {
            Route::get('/', [FlashSaleController::class, 'index'])->name('index');
            Route::get('/create', [FlashSaleController::class, 'create'])->middleware('permission:create orders')->name('create');
            Route::post('/', [FlashSaleController::class, 'store'])->middleware('permission:create orders')->name('store');
            Route::get('/{flash_sale}', [FlashSaleController::class, 'show'])->name('show');
            Route::get('/{flash_sale}/edit', [FlashSaleController::class, 'edit'])->middleware('permission:edit orders')->name('edit');
            Route::put('/{flash_sale}', [FlashSaleController::class, 'update'])->middleware('permission:edit orders')->name('update');
            Route::delete('/{flash_sale}', [FlashSaleController::class, 'destroy'])->middleware('permission:delete orders')->name('destroy');
        });
        Route::get('ajax/product-variants/{id}', [FlashSaleController::class, 'getVariantsByProduct']);
        Route::post('flash-sales/{id}/return-stock', [FlashSaleController::class, 'returnStock'])->middleware('permission:edit orders')->name('flash-sales.return-stock');

        Route::prefix('flash-sale-items')->name('flash-sale-items.')->middleware('permission:view orders')->group(function () {
            Route::get('/', [FlashSaleItemController::class, 'index'])->name('index');
            Route::get('/create', [FlashSaleItemController::class, 'create'])->middleware('permission:create orders')->name('create');
            Route::post('/', [FlashSaleItemController::class, 'store'])->middleware('permission:create orders')->name('store');
            Route::get('/{flash_sale_item}', [FlashSaleItemController::class, 'show'])->name('show');
            Route::get('/{flash_sale_item}/edit', [FlashSaleItemController::class, 'edit'])->middleware('permission:edit orders')->name('edit');
            Route::put('/{flash_sale_item}', [FlashSaleItemController::class, 'update'])->middleware('permission:edit orders')->name('update');
            Route::delete('/{flash_sale_item}', [FlashSaleItemController::class, 'destroy'])->middleware('permission:delete orders')->name('destroy');
        });

        // Blog Management
        Route::prefix('blogs')->name('blogs.')->middleware('permission:view blogs')->group(function () {
            Route::get('/', [BlogController::class, 'index'])->name('index');
            Route::get('/create', [BlogController::class, 'create'])->middleware('permission:create blogs')->name('create');
            Route::post('/', [BlogController::class, 'store'])->middleware('permission:create blogs')->name('store');
            Route::get('/trash', [BlogController::class, 'trash'])->name('trash');
            Route::get('/{blog}', [BlogController::class, 'show'])->name('show');
            Route::get('/{blog}/edit', [BlogController::class, 'edit'])->middleware('permission:edit blogs')->name('edit');
            Route::put('/{blog}', [BlogController::class, 'update'])->middleware('permission:edit blogs')->name('update');
            Route::delete('/{blog}', [BlogController::class, 'destroy'])->middleware('permission:delete blogs')->name('destroy');
            Route::put('/{id}/restore', [BlogController::class, 'restore'])->middleware('permission:edit blogs')->name('restore');
            Route::delete('/{id}/force-delete', [BlogController::class, 'forceDelete'])->middleware('permission:delete blogs')->name('forceDelete');
        });

        Route::get('reviews', [AdminProductReviewController::class, 'index'])->name('reviews.index');
        Route::get('reviews/{id}', [AdminProductReviewController::class, 'show'])->name('reviews.show');

        // Attribute Management
        Route::prefix('attributes')->name('attributes.')->group(function () {
            Route::get('/', [VariantAttributeTypeController::class, 'index'])->middleware('permission:view attributes')->name('index');
            Route::get('/create', [VariantAttributeTypeController::class, 'create'])->middleware('permission:create attributes')->name('create');
            Route::post('/', [VariantAttributeTypeController::class, 'store'])->middleware('permission:store attributes')->name('store');
            Route::get('/trash', [VariantAttributeTypeController::class, 'trash'])->middleware('permission:trash attributes')->name('trash');
            Route::post('/store-values', [VariantAttributeTypeController::class, 'storeValues'])->name('store-values');
            Route::get('/{attributeType}/edit', [VariantAttributeTypeController::class, 'edit'])->middleware('permission:edit attributes')->name('edit');
            Route::put('/{attributeType}', [VariantAttributeTypeController::class, 'update'])->middleware('permission:update attributes')->name('update');
            Route::delete('/{attributeType}', [VariantAttributeTypeController::class, 'destroy'])->middleware('permission:destroy attributes')->name('destroy');
            Route::post('/{attributeType}/restore', [VariantAttributeTypeController::class, 'restore'])->middleware('permission:restore attributes')->name('restore');
            Route::get('/{attributeType}/values', [ProductController::class, 'getAttributeValues'])->name('values');
        });

        // Product Management
        Route::prefix('products')->name('products.')->middleware('permission:view products')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->middleware('permission:create products')->name('create');
            Route::post('/', [ProductController::class, 'store'])->middleware('permission:create products')->name('store');
            Route::get('/trash', [ProductController::class, 'trash'])->name('trash');
            Route::post('/{product}/restore', [ProductController::class, 'restore'])->middleware('permission:edit products')->name('restore');
            Route::get('/{product}', [ProductController::class, 'show'])->name('show');
            Route::get('/{product}/edit', [ProductController::class, 'edit'])->middleware('permission:edit products')->name('edit');
            Route::put('/{product}', [ProductController::class, 'update'])->middleware('permission:edit products')->name('update');
            Route::delete('/{product}', [ProductController::class, 'destroy'])->middleware('permission:delete products')->name('destroy');
            Route::post('/check-duplicate-variants', [ProductController::class, 'checkDuplicateVariants'])->name('checkDuplicateVariants');
        });
        Route::get('attributes/{id}/values', [ProductController::class, 'getAttributeValues']);

        // Specification Management
        Route::prefix('specifications')->name('specifications.')->middleware('permission:view specifications')->group(function () {
            Route::get('/', [SpecificationController::class, 'index'])->name('index');
            Route::get('/create', [SpecificationController::class, 'create'])->name('create');
            Route::post('/', [SpecificationController::class, 'store'])->name('store');
            Route::get('/trash', [SpecificationController::class, 'trash'])->name('trash');
            Route::get('/{specification}', [SpecificationController::class, 'show'])->name('show');
            Route::get('/{specification}/edit', [SpecificationController::class, 'edit'])->name('edit');
            Route::put('/{specification}', [SpecificationController::class, 'update'])->name('update');
            Route::delete('/{specification}', [SpecificationController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/restore', [SpecificationController::class, 'restore'])->name('restore');
        });

        // Order Management
        Route::prefix('orders')->name('orders.')->middleware('permission:view orders')->group(function () {
            Route::get('/', [OrderController::class, 'index'])->name('index');
            Route::get('/{order}', [OrderController::class, 'show'])->name('show');
            Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->middleware('permission:edit orders')->name('updateStatus');
            Route::delete('/{order}', [OrderController::class, 'destroy'])->middleware('permission:delete orders')->name('destroy');
            Route::get('/{order}/export-invoice', [OrderController::class, 'exportInvoice'])->name('export-invoice');
        });

        // Role Management
        Route::prefix('roles')->name('roles.')->middleware('permission:addrole')->group(function () {
            Route::get('/{user}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{user}', [RoleController::class, 'update'])->name('update');
        });

        // Voucher Management
        Route::resource('vouchers', VoucherController::class)->middleware('permission:view vouchers');

        // Contact Management - Staff chỉ có quyền xem
        Route::prefix('contacts')->name('contacts.')->middleware('permission:view orders')->group(function () {
            Route::get('/', [AdminContactController::class, 'index'])->name('index');
            Route::get('/trash', [AdminContactController::class, 'trash'])->name('trash');
            Route::get('/{contact}', [AdminContactController::class, 'show'])->name('show');
            Route::delete('/{contact}', [AdminContactController::class, 'destroy'])->middleware('permission:delete orders')->name('delete');
            Route::patch('/restore/{id}', [AdminContactController::class, 'restore'])->middleware('permission:edit orders')->name('restore');
            Route::delete('/force-delete/{id}', [AdminContactController::class, 'forceDelete'])->middleware('permission:delete orders')->name('forceDelete');
        });

        // Subscriber Management - Staff chỉ có quyền xem
        Route::prefix('subscribers')->name('subscribers.')->middleware('permission:view orders')->group(function () {
            Route::get('/', [SubcriberController::class, 'index'])->name('index');
            Route::get('/trash', [SubcriberController::class, 'trash'])->name('trash');
            Route::delete('/{subscribers}', [SubcriberController::class, 'destroy'])->middleware('permission:delete orders')->name('delete');
            Route::patch('/restore/{id}', [SubcriberController::class, 'restore'])->middleware('permission:edit orders')->name('restore');
        });

        // Activity Management - Staff chỉ có quyền xem
        Route::prefix('activities')->name('activities.')->middleware('permission:view dashboard')->group(function () {
            Route::get('/', [ActivityController::class, 'index'])->name('index');
            Route::get('/user/{id}', [ActivityController::class, 'show'])->name('show');
        });

        // FAQ Management - Staff chỉ có quyền xem
        Route::prefix('faqs')->name('faqs.')->middleware('permission:view orders')->group(function () {
            Route::get('/', [FaqController::class, 'index'])->name('index');
            Route::get('/create', [FaqController::class, 'create'])->middleware('permission:create orders')->name('create');
            Route::post('/', [FaqController::class, 'store'])->middleware('permission:create orders')->name('store');
            Route::get('/trash', [FaqController::class, 'trash'])->name('trash');
            Route::get('/{faq}', [FaqController::class, 'show'])->name('show');
            Route::get('/{faq}/edit', [FaqController::class, 'edit'])->middleware('permission:edit orders')->name('edit');
            Route::put('/{faq}', [FaqController::class, 'update'])->middleware('permission:edit orders')->name('update');
            Route::delete('/{faq}', [FaqController::class, 'destroy'])->middleware('permission:delete orders')->name('destroy');
            Route::post('/{faq}/restore', [FaqController::class, 'restore'])->middleware('permission:edit orders')->name('restore');
            Route::delete('/{faq}/forceDelete', [FaqController::class, 'forceDelete'])->middleware('permission:delete orders')->name('forceDelete');
        });

        // Invoice Routes - Staff chỉ có quyền xem
        Route::prefix('invoices')->name('invoices.')->middleware('permission:view orders')->group(function () {
            Route::get('/', [InvoiceController::class, 'index'])->name('index');
            Route::get('/create', [InvoiceController::class, 'create'])->middleware('permission:create orders')->name('create');
            Route::post('/', [InvoiceController::class, 'store'])->middleware('permission:create orders')->name('store');
            Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
            Route::get('/{invoice}/edit', [InvoiceController::class, 'edit'])->middleware('permission:edit orders')->name('edit');
            Route::put('/{invoice}', [InvoiceController::class, 'update'])->middleware('permission:edit orders')->name('update');
            Route::delete('/{invoice}', [InvoiceController::class, 'destroy'])->middleware('permission:delete orders')->name('destroy');
        });
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'exportPdf'])->middleware('permission:view orders')->name('invoices.export-pdf');

        // Product Variants - Staff chỉ có quyền xem
        Route::prefix('variants')->name('variants.')->middleware('permission:view products')->group(function () {
            Route::get('/', [ProductVariantController::class, 'index'])->name('index');
            Route::get('/trash', [ProductVariantController::class, 'trash'])->name('trash');
            Route::post('/{id}/restore', [ProductVariantController::class, 'restore'])->middleware('permission:edit products')->name('restore');
            Route::put('/{variant}', [ProductVariantController::class, 'update'])->middleware('permission:edit products')->name('update');
            Route::delete('/{variant}', [ProductVariantController::class, 'destroy'])->middleware('permission:delete products')->name('destroy');
        });

        // Sitemap Management - Staff chỉ có quyền xem
        Route::prefix('sitemap')->name('sitemap.')->middleware('permission:view dashboard')->group(function () {
            Route::get('/', [SitemapController::class, 'index'])->name('index');
            Route::post('/generate', [SitemapController::class, 'generate'])->middleware('permission:create orders')->name('generate');
            Route::get('/view', [SitemapController::class, 'view'])->name('view');
        });

        // Robots Management - Staff chỉ có quyền xem
        Route::prefix('robots')->name('robots.')->middleware('permission:view dashboard')->group(function () {
            Route::get('/', [RobotController::class, 'index'])->name('index');
            Route::post('/update', [RobotController::class, 'update'])->middleware('permission:edit orders')->name('update');
        });

        // New route for checking variant slug
        Route::get('/admin/ajax/check-variant-slug', [ProductController::class, 'checkVariantSlug']);
    });

Route::post('/voucher/check', [ClientVoucherController::class, 'check'])->name('voucher.check');
Route::post('/voucher/available', [ClientVoucherController::class, 'getAvailableVouchers'])->name('voucher.available');

// Admin Profile Routes
Route::prefix('admin/profile')->name('admin.profile.')->middleware(['auth', 'role:admin|staff'])->group(function () {
    Route::get('/', [AdminProfileController::class, 'edit'])->name('index');
    Route::put('/', [AdminProfileController::class, 'update'])->name('update');
    Route::get('/password', [AdminProfileController::class, 'password'])->name('password');
    Route::put('/password', [AdminProfileController::class, 'updatePassword'])->name('update-password');
});

Route::get('/tim-kiem', [SearchController::class, 'index'])->name('search');
Route::get('/api/search-suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
