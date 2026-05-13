<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
// use Illuminate\Support\ServiceProvider;
use App\Models\Category;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\View;
use App\View\Composers\CartComposer;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('vi');
        Paginator::useBootstrapFive();
        // Share categories for menu - kiểm tra bảng tồn tại trước khi truy vấn
        if (\Illuminate\Support\Facades\Schema::hasTable('categories')) {
            $categories = Category::with('children')->whereNull('parent_id')->where('type', 1)->get();
            view()->share('categories', $categories);
        }
        $this->registerPolicies();

        // Bypass mọi permission nếu user là admin
        Gate::before(function ($user, $ability) {
            return $user->hasRole('admin') ? true : null;
        });

        // Đăng ký composer cho tất cả view
        View::composer('*', CartComposer::class);

        View::composer('client.partials.header', function ($view) {
            if (\Illuminate\Support\Facades\Schema::hasTable('categories')) {
                $categories = Category::where('type', 1)->where('status', 'active')->orderBy('order')->get();
                $view->with('categories', $categories);
            }
        });
    }
}
