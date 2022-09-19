<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function returns_unauthorized_if_guest()
    {
        $this->makeRequest()
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function returns_user_data_if_authenticated()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->makeRequest()
            ->assertStatus(200)
            ->assertJson($user->toArray());
    }


    private function makeRequest(): TestResponse
    {
        return $this->json('GET', '/api/users/current');
    }

}
