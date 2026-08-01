<?php

namespace App\Exceptions;

use App\Models\SystemEvent;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * Sistem sağlığı izleme sayfasına yazılmayacak, "normal" sayılan istisnalar.
     *
     * @var array<int, class-string>
     */
    protected $dontMonitor = [
        ValidationException::class,
        AuthenticationException::class,
        AuthorizationException::class,
        ModelNotFoundException::class,
        NotFoundHttpException::class,
        TokenMismatchException::class,
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $this->recordSystemEvent($e);
        });
    }

    /**
     * Yakalanmayan istisnaları/429 throttle bloklarını sistem sağlığı izleme tablosuna yazar.
     * Bu tablonun kendisiyle ilgili bir hata olursa (örn. tablo henüz oluşturulmadıysa)
     * sessizce yutulur — izleme mekanizması asla uygulamanın kendisini bozmamalı.
     */
    private function recordSystemEvent(Throwable $e): void
    {
        try {
            foreach ($this->dontMonitor as $ignored) {
                if ($e instanceof $ignored) {
                    return;
                }
            }

            $request = request();
            $isThrottle = $e instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException;

            $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;

            SystemEvent::log(
                source: 'web',
                type: $isThrottle ? 'throttle_blocked' : 'exception',
                severity: $isThrottle ? 'warning' : ($statusCode >= 500 ? 'critical' : 'warning'),
                message: $e->getMessage() ?: get_class($e),
                stackTrace: $isThrottle ? null : $e->getTraceAsString(),
                context: $this->buildContext($request, $statusCode)
            );
        } catch (Throwable $loggingFailure) {
            // izleme asla asıl isteği etkilemesin
        }
    }

    private function buildContext(?Request $request, int $statusCode): array
    {
        if (!$request) {
            return ['status_code' => $statusCode];
        }

        return [
            'status_code' => $statusCode,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => $request->user()?->id,
            'company_id' => $request->user()?->company_id,
        ];
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
