<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
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

    public function render($request, Throwable $e)
    {
        if ($e instanceof TokenMismatchException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Oturum süresi doldu. Lütfen sayfayı yenileyip tekrar deneyin.',
                ], 419);
            }

            $message = 'Oturum süresi doldu veya sayfa süresi geçti. Lütfen tekrar deneyin.';

            if ($request->is('login') || url()->previous() === route('login')) {
                return redirect()
                    ->route('login')
                    ->withInput($request->except('password', '_token'))
                    ->with('error', $message);
            }

            return redirect()
                ->back()
                ->withInput($request->except('password', '_token'))
                ->with('error', $message);
        }

        return parent::render($request, $e);
    }
}
