<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\AqiCnClient;

class AirQualityAssessmentController extends Controller
{
    public function __construct(private AqiCnClient $client)
    {
    }


    public function show(): Response
    {
        $user = Auth::user();
        try {
            if (!$user->hasSettings) {
                return response([
                    'aqi' => null,
                    'threshold' => null,
                    'message' => null
                ]);
            }

            $aqiAssessment = $this->client->getAirQualityAssessment($user->location, $user->threshold);
            return response($aqiAssessment, 200);
        } catch (\Exception $e) {
            return response(['message'=> 'There was a problem communicating with the Air Quality data service. Please try again later.'], $e->getCode());
        }
    }

}
