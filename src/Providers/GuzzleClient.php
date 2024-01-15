<?php

namespace AhmedEbead\Moyasar\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;


class GuzzleClient
{
    private ?string $baseUrl;
    private string $authType;
    private string $authToken;

    public function __construct(?string $baseUrl = "", $authType = "Bearer ", $authToken = "")
    {
        $this->setBaseUrl($baseUrl);
        $this->setAuthType($authType);
        $this->setAuthToken($authToken);
    }

    public function setBaseUrl(?string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function setAuthType(string $authType): self
    {
        $this->authType = $authType;
        return $this;
    }

    public function setAuthToken(string $authToken): self
    {
        $this->authToken = $authToken;
        return $this;
    }

    public function client(): Client
    {
        return new Client([
            'headers' => [
                "content-type" => "application/json",
                "Authorization" => $this->authType . $this->authToken,
            ]
        ]);
    }

    private function getUrl($url)
    {
        return str_starts_with(strtolower($url), 'http') ? $url : $this->baseUrl . $url;
    }

    public function get($url,  $params = [],bool $withError = false): GuzzleClientResponse
    {
        try {
            $response = $this->client()->get($this->getUrl($url), [
                "form_params" => $params
            ]);
            return new GuzzleClientResponse(
                status: $response->getStatusCode(),
                result: json_decode((string)$response->getBody())
            );
        } catch (ClientException $exception) {
            if($withError) {
                return new GuzzleClientResponse(
                    status: $exception->getCode(),
                    result: (object)["error" => $exception->getMessage()]
                );
            }
            else throw $exception;
        }
    }

    public function post($url, $body, $isFormData = false, bool $withError = false): GuzzleClientResponse
    {
        try {
            if ($isFormData)
                $options["form_params"] = $body;
            else
                $options["body"] = json_encode($body);
            $response = $this->client()->post($this->getUrl($url), array_merge($options, [
                'headers' => [
                    //'Content-Type' => 'application/json',
                ]
            ]));
            return new GuzzleClientResponse(
                status: $response->getStatusCode(),
                result: json_decode((string)$response->getBody())
            );
        } catch (ClientException $exception) {
            if($withError) {
                return new GuzzleClientResponse(
                    status: $exception->getCode(),
                    result: (object)["error" => $exception->getMessage()]
                );
            }
            else throw $exception;
        }
    }
    public function put($url, $body, $isFormData = false): GuzzleClientResponse
    {
        try {
            if ($isFormData) {
                $options["form_params"] = $body;
            } else
                $options["body"] = json_encode($body);
            $response = $this->client()->put($this->getUrl($url), array_merge($options, [
                'headers' => [
                    //'Content-Type' => 'application/json',
                ]
            ]));
            return new GuzzleClientResponse(
                status: $response->getStatusCode(),
                result: json_decode((string)$response->getBody())
            );
        } catch (ClientException $exception) {
            throw $exception;
        }
    }
}
