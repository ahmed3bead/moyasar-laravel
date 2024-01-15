<?php

namespace AhmedEbead\Moyasar\Providers;

class GuzzleClientResponse
{
    private int $status;
    private ?object $result;

    public function __construct(int $status, $result)
    {
        $this->status = $status;
        $this->result = $result;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function isSuccess(): bool
    {
        return $this->status == 200 or $this->status == 201;
    }

    public function getResult(): ?object
    {
        return $this->result;
    }

    public function toArray($key = null): ?array
    {
        $result = json_decode(json_encode($this->getResult()), true);
        if ($key)
            return $result[$key];
        return $result;
    }

}
