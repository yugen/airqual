<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use GuzzleHttp\ClientInterface;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\AqiCnClient;
use GuzzleHttp\Exception\ClientException;
use App\Http\Requests\StoreSettingsRequest;
use App\Exceptions\UnsupportedLocationException;

class SettingsController extends Controller
{
    public function __construct(private AqiCnClient $client)
    {
    }


    public function store(StoreSettingsRequest $request): Response
    {
        try {
            $assessment = $this->client->getAirQualityAssessment($request->location, $request->threshold);

            Auth::user()->update([
                'threshold' => $request->threshold,
                'location' => $request->location
            ]);

            return response($assessment, 200);

        } catch (UnsupportedLocationException $th) {
            return response([
                'aqi' => null,
                'threshold' => null,
                'message' => $th->getMessage()
            ]);
        } catch (ClientException $th) {
            return response([
                'message' => 'There was a problem communicating with the Air Quality data service: '.$th->getMessage(),
            ], $th->getCode());
        }
    }

}
