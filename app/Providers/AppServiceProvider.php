<?php

namespace App\Providers;

use App\Http\Resources\TripsResource;
use App\Models\Trips;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Validation\ValidationException;
use Filament\Pages\Page;
use Illuminate\Support\ServiceProvider;
use Filament\Notifications\Notification;

use Illuminate\Support\Facades\Cache;
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
        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
            Notification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };

        View::composer('welcome',function($view){
            $trips= Cache::remember('trips',60*60,function(){
                return TripsResource::collection(Trips::all());
            });
           $view->with('trips',$trips);

        });

        Vite::prefetch(3);
    }
}
