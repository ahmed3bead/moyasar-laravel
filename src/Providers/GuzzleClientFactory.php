<?php

namespace AhmedEbead\Moyasar\Providers;

use GuzzleHttp\Client;
use AhmedEbead\Moyasar\Moyasar;

class GuzzleClientFactory
{
    public function build(): Client
    {
        return new Client($this->options());
    }

    public function options(): array
    {
        return [
            'base_uri' => Moyasar::CURRENT_VERSION_URL,
            'headers' => [
                "content-type" => "application/json",
                "Authorization" => "Basic " . base64_encode(config('moyasar.secret_key'))
            ]
        ];
    }
}
