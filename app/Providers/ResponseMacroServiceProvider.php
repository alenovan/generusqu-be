<?php

namespace App\Providers;

use App\Traits\GlobalTrait;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
{

    use GlobalTrait;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($data = [], $message = '', $statusCode = 200) {
            return Response::json([
                'status' => true,
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        });

        Response::macro('error', function ($message = 'bad request', $statusCode = 400) {
            $error_msg = 'Aksi gagal ';

            if ($statusCode == 0) $statusCode = 400;

            if ($statusCode > 599) $statusCode = 500;

            if ($statusCode == 500) {
                $message = (env('APP_ENV') == "local" && env('APP_DEBUG') == "true") ? $error_msg . " [" . $message . "]" : $error_msg;
            }

            return Response::json([
                'status' => false,
                'message' => GlobalTrait::jsonCheck($message) ? json_decode($message, true) : $message,
            ], $statusCode);
        });
    }
}
