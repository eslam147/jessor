<?php

namespace App\Exceptions;

use Throwable;
use Sentry\Laravel\Integration;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];
    public function report(Throwable $exception)
    {
        $tenantId = null;
        if (tenancy()->initialized) {
            $tenantId = tenancy()->tenant->id;
            Log::withContext(['tenant_id' => tenancy()->tenant->id]);
        }

        parent::report($exception);
        if (! App::environment('local') && $this->shouldReport($exception) && app()->bound('sentry')) {
            \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($tenantId) {
                if (session()->isStarted()) {
                    if ($user = session()->get(Auth::getName())) {
                        $scope->setTag('user_id', $user);
                    }
                }
                $scope->setTag('tenant_id', $tenantId);
            });
            app('sentry')->captureException($exception);
        }
    }

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            Integration::captureUnhandledException($e);
        });
    }
}
