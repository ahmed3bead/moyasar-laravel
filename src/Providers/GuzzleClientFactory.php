<?php

namespace AhmedEbead\Moyasar\Providers;

use AhmedEbead\Moyasar\Moyasar;
use GuzzleHttp\Client;

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
                "Authorization" => "Basic " . base64_encode(config('moyasar.secret_key'))
            ]
        ];
    }
}
