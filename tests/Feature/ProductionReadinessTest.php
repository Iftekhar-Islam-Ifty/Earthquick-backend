<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
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

    public function test_catalog_seeding_never_installs_a_default_admin_or_resets_an_opt_in_admin(): void
    {
        $names = ['EARTHQUICK_SEED_ADMIN_EMAIL', 'EARTHQUICK_SEED_ADMIN_PASSWORD'];
        $previous = [];
        foreach ($names as $name) {
            $previous[$name] = [getenv($name), $_ENV[$name] ?? null, $_SERVER[$name] ?? null];
        }

        try {
            foreach ($names as $name) {
                putenv($name.'=');
                $_ENV[$name] = $_SERVER[$name] = '';
            }

            Artisan::call('db:seed', ['--force' => true]);
            $this->assertDatabaseMissing('users', ['email' => 'admin@earthquick.com']);

            $email = 'bootstrap-admin@example.test';
            putenv('EARTHQUICK_SEED_ADMIN_EMAIL='.$email);
            $_ENV['EARTHQUICK_SEED_ADMIN_EMAIL'] = $_SERVER['EARTHQUICK_SEED_ADMIN_EMAIL'] = $email;
            putenv('EARTHQUICK_SEED_ADMIN_PASSWORD=first-private-passphrase');
            $_ENV['EARTHQUICK_SEED_ADMIN_PASSWORD'] = $_SERVER['EARTHQUICK_SEED_ADMIN_PASSWORD'] = 'first-private-passphrase';

            Artisan::call('db:seed', ['--force' => true]);
            $admin = User::where('email', $email)->firstOrFail();
            $this->assertTrue($admin->is_admin);
            $this->assertTrue(Hash::check('first-private-passphrase', $admin->password));

            putenv('EARTHQUICK_SEED_ADMIN_PASSWORD=changed-passphrase');
            $_ENV['EARTHQUICK_SEED_ADMIN_PASSWORD'] = $_SERVER['EARTHQUICK_SEED_ADMIN_PASSWORD'] = 'changed-passphrase';
            Artisan::call('db:seed', ['--force' => true]);

            $this->assertSame(1, User::where('email', $email)->count());
            $this->assertTrue(Hash::check('first-private-passphrase', $admin->fresh()->password));
        } finally {
            foreach ($previous as $name => [$process, $environment, $server]) {
                $process === false ? putenv($name) : putenv($name.'='.$process);
                if ($environment === null) {
                    unset($_ENV[$name]);
                } else {
                    $_ENV[$name] = $environment;
                }
                if ($server === null) {
                    unset($_SERVER[$name]);
                } else {
                    $_SERVER[$name] = $server;
                }
            }
        }
    }
}
