<?php

namespace Tests\Feature;

use Mockery;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @test
     */
    public function redirects_to_the_correct_provider()
    {
        $this->call('GET', '/auth/redirect?provider=google')
            ->assertRedirectContains('https://accounts.google.com/o/oauth2/auth?client_id='.config('services.google.client_id'));

        $this->call('GET', '/auth/redirect?provider=github')
            ->assertRedirectContains('https://github.com/login/oauth/authorize?client_id='.config('services.github.client_id'));
    }

    /**
     * @test
     */
    public function creates_a_new_user_if_the_provided_user_does_not_exist()
    {
        $user = $this->mockAbstractUser();

        $this->call('GET', '/auth/callback/github', [
            'code' => uniqid(),
            'state' => uniqid()
        ])->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'provider' => 'github',
            'provider_id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ]);
    }

    /**
     * @test
     */
    public function updates_an_existing_user_if_the_provided_user_exists()
    {
        Carbon::setTestNow('2022-09-17');
        $user = User::factory()->create([
            'provider' => 'github',
            'provider_id' => 123456,
        ]);
        Carbon::setTestNow('2022-09-18');
        $user = $this->mockAbstractUser([
            'id' => 123456,
            'name' => $this->faker->name(),
            'email' => $this->faker->email()
        ]);

        $this->call('GET', '/auth/callback/github', [
            'code' => uniqid(),
            'state' => uniqid()
        ])->assertRedirect('/');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'provider' => 'github',
            'provider_id' => 123456,
            'updated_at' => Carbon::now()
        ]);

    }


    private function mockAbstractUser($data = null)
    {
        $data = $data ?? [
            'id' => rand(),
            'name' => $this->faker->name,
            'email' => $this->faker->email
        ];
        $user = Mockery::mock('Laravel\Socialite\Two\User');
        $user
            ->shouldReceive('getId')->andReturn($data['id'])
            ->shouldReceive('getName')->andReturn($data['name'])
            ->shouldReceive('getEmail')->andReturn($data['email']);

        $user->token = uniqid();
        $user->refreshToken = uniqid();
        Socialite::shouldReceive('driver->stateless->user')->andReturn($user);

        return $user;
    }


}
