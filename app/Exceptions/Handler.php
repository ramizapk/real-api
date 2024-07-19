<?php

// namespace App\Exceptions;

// use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
// use Throwable;

// class Handler extends ExceptionHandler
// {
// /**
// * The list of the inputs that are never flashed to the session on validation exceptions.
// *
// * @var array<int, string>
// */
// protected $dontFlash = [
// 'current_password',
// 'password',
// 'password_confirmation',
// ];

// /**
// * Register the exception handling callbacks for the application.
// */
// public function register(): void
// {
// $this->reportable(function (Throwable $e) {
// //
// });
// }
// }




namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\UnauthorizedException;
use MessageBird\Exceptions\AuthenticateException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->errorResponse('Resource not found', 404);
        } elseif ($exception instanceof \Illuminate\Validation\ValidationException) {
            return $this->errorResponse($exception->validator->errors()->first(), 422);
        }

        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            return $this->errorResponse($exception->getMessage(), 422);
        }

        // return $this->errorResponse('Internal Server Error', 500);
        return response()->json([
            'status' => 'error',
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
            'code' => $exception->getCode(),
        ], 500);

    }
}