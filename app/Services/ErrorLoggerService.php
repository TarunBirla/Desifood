<?php

namespace App\Services;

use App\Models\ErrorLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class ErrorLoggerService
{
    /**
     * Keys whose values must be masked in request logs.
     */
    protected static array $sensitiveKeys = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'secret',
        'credit_card',
        'card_number',
        'cvv',
        'authorization',
        'api_key',
    ];

    /**
     * Log the given exception to the database.
     *
     * @param Throwable $exception
     * @return ErrorLog|null
     */
    public static function log(Throwable $exception): ?ErrorLog
    {
        try {
            $isConsole = app()->runningInConsole();

            // Extract User Details safely
            $userId = null;
            $userDetails = null;

            try {
                if (Auth::check()) {
                    $user = Auth::user();
                    if ($user) {
                        $userId = $user->id;
                        $userDetails = [
                            'id' => $user->id,
                            'name' => $user->name ?? null,
                            'email' => $user->email ?? null,
                            'role' => isset($user->role) ? $user->role->name : null,
                        ];
                    }
                }
            } catch (Throwable $userError) {
                // Ignore auth inspection errors if session is uninitialized
            }

            // Extract Request / Context Safely
            $url = null;
            $method = null;
            $ipAddress = null;
            $userAgent = null;
            $requestDetails = null;

            if ($isConsole && !app()->has('request')) {
                $url = 'CLI: ' . implode(' ', $_SERVER['argv'] ?? ['artisan']);
                $method = 'CLI';
                $requestDetails = [
                    'environment' => 'cli',
                    'argv' => $_SERVER['argv'] ?? [],
                ];
            } else {
                try {
                    $request = request();
                    if ($request) {
                        $url = $request->fullUrl();
                        $method = $request->method();
                        $ipAddress = $request->ip();
                        $userAgent = $request->userAgent();

                        $route = $request->route();
                        $routeName = $route ? ($route->getName() ?? $route->getActionName()) : null;

                        $inputs = static::sanitizeData($request->all());

                        $requestDetails = [
                            'environment' => $isConsole ? 'cli' : 'web',
                            'route' => $routeName,
                            'inputs' => $inputs,
                            'query' => static::sanitizeData($request->query()),
                            'headers' => [
                                'accept' => $request->header('accept'),
                                'content-type' => $request->header('content-type'),
                                'referer' => $request->header('referer'),
                            ],
                        ];
                    }
                } catch (Throwable $reqError) {
                    // Fallback CLI info if request resolution failed
                    $url = 'CLI: ' . implode(' ', $_SERVER['argv'] ?? ['artisan']);
                    $method = 'CLI';
                }
            }

            // Create Database Error Log
            return ErrorLog::create([
                'user_id' => $userId,
                'user_details' => $userDetails,
                'exception_type' => get_class($exception),
                'message' => $exception->getMessage() ?: get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'url' => $url,
                'method' => $method,
                'request_details' => $requestDetails,
                'stack_trace' => $exception->getTraceAsString(),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        } catch (Throwable $loggingError) {
            // Fallback to file logging if DB logging fails to prevent secondary crashes
            try {
                Log::error('Failed to write exception to database error_logs: ' . $loggingError->getMessage(), [
                    'original_exception' => $exception->getMessage(),
                    'logging_exception' => $loggingError->getTraceAsString(),
                ]);
            } catch (Throwable $ignore) {
                // Secondary fallback suppressed
            }

            return null;
        }
    }

    /**
     * Recursively mask sensitive parameters.
     *
     * @param array $data
     * @return array
     */
    protected static function sanitizeData(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array(strtolower((string)$key), static::$sensitiveKeys, true)) {
                $data[$key] = '***MASKED***';
            } elseif (is_array($value)) {
                $data[$key] = static::sanitizeData($value);
            }
        }

        return $data;
    }
}
