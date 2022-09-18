<?php

namespace App\Services;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use App\Exceptions\UnsupportedLocationException;

class AqiCnClient
{
    public function __construct(private ClientInterface $httpClient)
    {
    }

    /**
     * Run a get request against eh aqicn api
     *
     * @param string $path Path of the endpoint relative to the domain
     * @param array $params Associative array of parameters for the query string
     * @param array $headers Optional headers to send with the request
     *
     * @return GuzzleHttp\Psr7\Response
     */
    public function get(string $path, array $params = [], $headers = []): Response
    {
        $queryParams = array_merge(
            $params,
            ['token' => config('services.aqicn_token')]
        );

        $url = config('services.aqicn_url').$path.'?'.http_build_query($queryParams);

        return $this->httpClient->request('GET', $url, ['headers' => $headers]);
    }

    /**
     * Run a search against the aqicn search endpoint
     *
     * @param string $keyword
     *
     * @return object Json decoded response data
     */
    public function search(string $keyword): object
    {
        $response = $this->get('search/', ['keyword' => $keyword]);
        $data = json_decode($response->getBody()->getContents());

        return $data;
    }

    /**
     * Get the aqi for a the first station returned for a location
     *
     * @param string $location
     *
     * @throws UnsupportedLocationException
     * @return array Associative array
     */
    public function getAirQualityAssessment(string $location, int $threshold): array
    {
            $data = $this->search($location);

            if (count($data->data) == 0) {
                throw new UnsupportedLocationException($location);
            }

            return [
                'aqi' => (int)$data->data[0]->aqi,
                'threshold' => $threshold,
                'message' => $this->getMessage((int)$data->data[0]->aqi, $threshold)
            ];
    }

    private function getMessage(int $aqi, int $threshold): string
    {
        if ($aqi > $threshold) {
            return 'Don\'t breathe out there.';
        }

        return 'It\'s safe to go outside.';
    }

}
