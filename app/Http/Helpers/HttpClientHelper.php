<?php


namespace App\Http\Helpers;


use GuzzleHttp\Exception\ClientException;
use Log;
use Request;

class HttpClientHelper
{
    /**
     * @param string $route_name
     * @param array $parameters
     * @return mixed|null
     * @throws \Exception
     */
    public function get(string $route_name, $parameters = array())
    {
        try {
            $httpRequest = Request::create(route($route_name), 'GET', $parameters);
            $httpResponse = app()->handle($httpRequest);
            $responseBody = $httpResponse->getContent();
            if ($responseBody) {
                return json_decode($responseBody, true);
            }
        } catch (ClientException $e) {
            Log::error((string)$e);
            return null;
        }
    }

    /**
     * @param string $route_name
     * @param array $parameters
     * @return mixed|null
     * @throws \Exception
     */
    public function getWithUrl(string $uri, $parameters = array())
    {
        try {
            $httpRequest = Request::create($uri, 'GET', $parameters);
            $httpResponse = app()->handle($httpRequest);
            $responseBody = $httpResponse->getContent();
            if ($responseBody) {
                return json_decode($responseBody, true);
            }
        } catch (ClientException $e) {
            Log::error((string)$e);
            return null;
        }
    }
}