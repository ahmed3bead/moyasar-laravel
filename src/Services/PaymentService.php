<?php

namespace AhmedEbead\Moyasar\Services;

use AhmedEbead\Moyasar\Contracts\HttpClient as ClientContract;
use AhmedEbead\Moyasar\Exceptions\ApiException;
use AhmedEbead\Moyasar\PaginationResult;
use AhmedEbead\Moyasar\Payment;
use AhmedEbead\Moyasar\Providers\GuzzleClient;
use AhmedEbead\Moyasar\Resource;
use AhmedEbead\Moyasar\Search;

class PaymentService
{
    const PAYMENT_PATH = 'payments';

    /**
     * @var ClientContract
     */
    protected $client = null;

    public function __construct($client = null)
    {

        if ($client == null) {
            $config = config('moyasar');
            $client = new GuzzleClient(
                baseUrl: $config['api_url'],
                authType: "Basic ",
                authToken: base64_encode($config['secret_key'])
            );

        }
        $this->client = $client;
    }

    /**
     * Fetches a payment from Moyasar servers
     *
     * @param string $id
     * @return Payment
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \AhmedEbead\Moyasar\Exceptions\ApiException
     */
    public function fetch($id)
    {
        $response = $this->client->get(self::PAYMENT_PATH . "/$id");
        $payment = Payment::fromArray($response->toArray());
        $payment->setClient($this->client);
        return $payment;
    }

    /**
     * Fetches all payments from Moyasar servers
     *
     * @param Search|array|null $query
     * @return PaginationResult
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \AhmedEbead\Moyasar\Exceptions\ApiException
     */
    public function all($query = null)
    {
        if ($query instanceof Search) {
            $query = $query->toArray();
        }
        $response = $this->client->get(self::PAYMENT_PATH, request()->query());
        $data = $response->toArray();
        $meta = $data['meta'];
        $payments = array_map(function ($i) {
            $payment = Payment::fromArray($i);
            $payment->setClient($this->client);
            return $payment;
        }, $data['payments']);
        return PaginationResult::fromArray($meta)->setResult($payments);
    }

    /**
     * Process a payment.
     *
     * @type array $source {
     *         The payment source information.
     *
     * @return Resource
     */

    public function create(array $paymentDetails): Resource
    {
        if (empty($paymentDetails['currency'])) {
            $paymentDetails['currency'] = config('moyasar.default_currency');
        }
        $paymentDetails['callback_url'] = config('moyasar.callback_url');
        $response = $this->client->post(self::PAYMENT_PATH, $paymentDetails);
        $payment = Payment::fromArray($response->toArray());
        $payment->setClient($this->client);
        return $payment;
    }

    // Update meta and description
    public function update($id, array $newData): Resource
    {
        $response = $this->client->put(self::PAYMENT_PATH . '/' . $id, $newData);
        $payment = Payment::fromArray($response->toArray());
        $payment->setClient($this->client);
        return $payment;
    }

    public function refund($id, int $amount): Resource
    {
        $response = $this->client->post(self::PAYMENT_PATH . '/' . $id . '/refund', ['amount' => $amount]);
        $payment = Payment::fromArray($response->toArray());
        $payment->setClient($this->client);
        return $payment;
    }

    public function capture($id, int $amount): Resource
    {
        $response = $this->client->post(self::PAYMENT_PATH . '/' . $id . '/capture', ['amount' => $amount]);
        $payment = Payment::fromArray($response->toArray());
        $payment->setClient($this->client);
        return $payment;
    }
}
