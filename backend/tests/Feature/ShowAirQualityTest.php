<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\ClientInterface;
use Tests\Traits\MocksHttpClient;
use Illuminate\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowAirQualityTest extends TestCase
{
    use RefreshDatabase;
    use MocksHttpClient;

    public function setup():void
    {
        parent::setup();
        $this->user = User::factory()->create(['threshold' => 100, 'location' => 'chicago']);
    }

    /**
     * @test
     */
    public function guest_cannot_get_an_air_quality_assessment()
    {
        $this->makeRequest()
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function a_user_can_get_an_air_quality_assessment_for_their_settings()
    {
        Sanctum::actingAs($this->user);

        app()->bind(ClientInterface::class, function () {
            return $this->setupMockClient([
                new Response(200, [], $this->loadTestFile('search_chicago.json')),
                new Response(200, [], $this->loadTestFile('search_chicago.json'))
            ]);
        });

        $this->makeRequest()
            ->assertStatus(200)
            ->assertJson([
                'aqi' => 54,
                'threshold' => 100,
                'message' => 'It\'s safe to go outside.'
            ]);

        $this->user->update(['threshold' => 53]);

        $this->makeRequest()
            ->assertStatus(200)
            ->assertJson([
                'aqi' => 54,
                'threshold' => 53,
                'message' => 'Don\'t breathe out there.'
            ]);
    }

    /**
     * @test
     */
    public function passes_api_response_errors_to_client()
    {
        Sanctum::actingAs($this->user);

        app()->bind(ClientInterface::class, function () {
            return $this->setupMockClient([
                new Response(500, [], json_encode(['error' => 'Everything is on fire']))
            ]);
        });

        $this->makeRequest()
            ->assertStatus(500)
            ->assertJson([
                'message' => 'There was a problem communicating with the Air Quality data service. Please try again later.',
            ]);
    }


    /**
     * @test
     */
    public function a_user_with_no_settings_gets_an_empty_assessment()
    {
        $user = User::factory()->make(['location' => null, 'threshold' => null]);
        Sanctum::actingAs($user);

        $this->makeRequest()
            ->assertStatus(200)
            ->assertJson([
                'aqi' => null,
                'threshold' => null,
                'message' => null
            ]);
    }


    private function makeRequest(): TestResponse
    {
        return $this->json('GET', '/api/air-quality-assessment');
    }
}
