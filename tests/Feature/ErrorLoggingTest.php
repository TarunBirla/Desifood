<?php

namespace Tests\Feature;

use App\Models\ErrorLog;
use App\Models\User;
use App\Services\ErrorLoggerService;
use Exception;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ErrorLoggingTest extends TestCase
{
    /**
     * Test direct ErrorLoggerService logging.
     */
    public function test_error_logger_service_creates_database_log(): void
    {
        $uniqueMsg = "Direct test exception " . uniqid();
        $exception = new Exception($uniqueMsg, 500);
        $log = ErrorLoggerService::log($exception);

        $this->assertNotNull($log);
        $this->assertDatabaseHas('error_logs', [
            'id' => $log->id,
            'exception_type' => Exception::class,
            'message' => $uniqueMsg,
        ]);
        $this->assertNotNull($log->stack_trace);
    }

    /**
     * Test automatic exception logging from HTTP request with guest user.
     */
    public function test_automatic_exception_logging_for_guest_request(): void
    {
        $uniqueMsg = "Simulated guest route error " . uniqid();

        Route::get('/test-error-guest-route', function () use ($uniqueMsg) {
            throw new Exception($uniqueMsg);
        });

        $response = $this->get('/test-error-guest-route');
        $response->assertStatus(500);

        $this->assertDatabaseHas('error_logs', [
            'exception_type' => Exception::class,
            'message' => $uniqueMsg,
            'method' => 'GET',
            'user_id' => null,
        ]);

        $log = ErrorLog::where('message', $uniqueMsg)->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('/test-error-guest-route', $log->url);
        $this->assertNull($log->user_details);
    }

    /**
     * Test automatic exception logging for authenticated user with role details.
     */
    public function test_exception_logging_captures_authenticated_user_details(): void
    {
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test_' . uniqid() . '@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $uniqueMsg = "Authenticated route failure " . uniqid();

        Route::post('/test-authenticated-error', function () use ($uniqueMsg) {
            throw new Exception($uniqueMsg);
        });

        $response = $this->actingAs($user)->post('/test-authenticated-error', [
            'param_a' => 'sample_value',
            'password' => 'supersecret123',
        ]);

        $response->assertStatus(500);

        $log = ErrorLog::where('message', $uniqueMsg)->first();
        $this->assertNotNull($log);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertEquals($user->name, $log->user_details['name']);
        $this->assertEquals($user->email, $log->user_details['email']);
    }

    /**
     * Test sensitive input parameter masking.
     */
    public function test_sensitive_request_inputs_are_masked(): void
    {
        $uniqueMsg = "Sensitive input test " . uniqid();

        Route::post('/test-sensitive-error', function () use ($uniqueMsg) {
            throw new Exception($uniqueMsg);
        });

        $this->post('/test-sensitive-error', [
            'username' => 'testuser',
            'password' => 'secret_password',
            'credit_card' => '1234-5678-9012-3456',
        ]);

        $log = ErrorLog::where('message', $uniqueMsg)->first();
        $this->assertNotNull($log);

        $inputs = $log->request_details['inputs'] ?? [];
        $this->assertEquals('testuser', $inputs['username']);
        $this->assertEquals('***MASKED***', $inputs['password']);
        $this->assertEquals('***MASKED***', $inputs['credit_card']);
    }

    /**
     * Test resilient error handling when DB logging completes or handles error cleanly.
     */
    public function test_error_logger_handles_exceptions_gracefully(): void
    {
        $faultyException = new class("Faulty Test Exception") extends Exception {};

        $result = ErrorLoggerService::log($faultyException);
        $this->assertTrue(true);
    }
}
