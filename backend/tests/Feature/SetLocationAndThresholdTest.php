<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use GuzzleHttp\ClientInterface;
use Laravel\Sanctum\Sanctum;
use GuzzleHttp\Psr7\Response;
use Tests\Traits\MocksHttpClient;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SetLocationAndThresholdTest extends TestCase
{
    use RefreshDatabase;
    use MocksHttpClient;


    public function setup():void
    {
        parent::setup();
        $this->user = User::factory()->create();
    }

    /**
     * @test
     */
    public function guest_cannot_set_a_location()
    {
        $this->makeRequest()
            ->assertStatus(401);

    }

    /**
     * @test
     */
    public function user_can_set_for_a_location()
    {
        Sanctum::actingAs($this->user);
        app()->bind(ClientInterface::class, function () {
            return $this->setupMockClient([new Response(200, [], $this->loadTestFile('search_chicago.json'))]);
        });

        $this->makeRequest()
            ->assertStatus(200)
            ->assertJson([
                'aqi' => 54,
                'threshold' => 100,
                'message' => 'It\'s safe to go outside.'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'threshold' => 100,
            'location' => 'chicago'
        ]);
    }

    /**
     * @test
     */
    public function user_receives_a_notification_if_location_has_no_stations()
    {
        Sanctum::actingAs($this->user);
        app()->bind(ClientInterface::class, function () {
            return $this->setupMockClient([new Response(200, [], $this->loadTestFile('search_no_results.json'))]);
        });

        $this->makeRequest()
            ->assertStatus(200)
            ->assertJson([
                'aqi' => null,
                'threshold' => null,
                'message' => 'No stations where found near your location.  Try searching for another city or a state/province.'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->user->id,
            'threshold' => null,
            'location' => null
        ]);
    }

    /**
     * @test
     */
    public function validates_input()
    {
        Sanctum::actingAs($this->user);
        $this->makeRequest([])
            ->assertStatus(422)
            ->assertJsonFragment([
                'errors' => [
                    'location' => ['This is required.'],
                    'threshold' => ['This is required.']
                ]
            ]);

        $this->makeRequest(['threshold' => 'bob'])
            ->assertStatus(422)
            ->assertJsonFragment(['threshold' => ['This must be a number.']]);

        $this->makeRequest(['threshold' => 501])
            ->assertStatus(422)
            ->assertJsonFragment(['threshold' => ['This must not be greater than 500.']]);

        $this->makeRequest(['threshold' => -1])
            ->assertStatus(422)
            ->assertJsonFragment(['threshold' => ['This must be at least 0.']]);
    }


    private function makeRequest($data = null): TestResponse
    {
        $data = $data ?? ['location' => 'chicago', 'threshold' => 100];
        return $this->json('POST', '/api/settings', $data);
    }


}
