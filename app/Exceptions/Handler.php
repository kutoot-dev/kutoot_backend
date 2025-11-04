<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

use Illuminate\Http\Request;
use  Illuminate\Auth\AuthenticationException;
use   Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Arr;
class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if($request->expectsJson()){
            return response()->json(['message' => 'UnAuthenticated'], 401);
        }
        $guard=Arr::get($exception->guards(),'0');
        switch($guard){
            case 'admin':
                $login="/admin/login";
            break;

            default:
                $login="/seller/login";
        }

        return Redirect()->guest($login);
    }

}
