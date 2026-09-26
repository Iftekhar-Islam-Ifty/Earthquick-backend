<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    public function test_web_responses_include_baseline_security_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), geolocation=(), microphone=()');
    }

    public function test_health_endpoint_is_available_with_security_headers(): void
    {
        $this->get('/up')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function test_login_is_temporarily_limited_after_repeated_failed_attempts(): void
    {
        $identifier = 'rate-limit-'.uniqid().'@example.test';
        $throttleKey = 'login:'.$identifier.'|127.0.0.1';

        RateLimiter::clear($throttleKey);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from('/login')->post('/login', [
                'identifier' => $identifier,
                'password' => 'wrong-password',
            ])->assertRedirect('/login');
        }

        $this->from('/login')->post('/login', [
            'identifier' => $identifier,
            'password' => 'wrong-password',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors('identifier');

        RateLimiter::clear($throttleKey);
    }
}
