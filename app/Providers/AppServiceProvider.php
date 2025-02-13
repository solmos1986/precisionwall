<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
     /**
     * emails
     * Note: this validates multiple emails in coma separated string.
     */
    Validator::extend('emails', function ($attribute, $value, $parameters, $validator) {
        $emails = explode(",", $value);
        foreach ($emails as $k => $v) {
            if (isset($v) && $v !== "") {
                $temp_email = trim($v);
                if (!filter_var($temp_email, FILTER_VALIDATE_EMAIL)) {
                    return false;
                }
            }
        }
        return true;
    }, 'Error message - email is not in right format');

    }
}
