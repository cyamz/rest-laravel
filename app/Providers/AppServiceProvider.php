<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        \Schema::defaultStringLength(191);

        \DB::listen(function ($query) {
            $tmp = str_replace('?', '"' . '%s' . '"', $query->sql);
            $qBindings = [];
            foreach ($query->bindings as $key => $value) {
                if (is_numeric($key)) {
                    $qBindings[] = $value;
                } else {
                    $tmp = str_replace(':' . $key, '"' . $value . '"', $tmp);
                }
            }
            $tmp = vsprintf($tmp, $qBindings);
            $sql = str_replace("\\", "", $tmp);

            $request_id = request()->request_id ?? '';
            $request_id_str = '[' . $request_id . ']';
            $time_str = 'execution time: ' . $query->time . 'ms;';

            $str = implode(';', [
                $request_id_str, $sql, $time_str, 
            ]);

            \Log::channel('sql')->info($str);
        });
    }
}
