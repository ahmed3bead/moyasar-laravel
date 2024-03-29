<?php

namespace AhmedEbead\Moyasar\Services;

use AhmedEbead\Moyasar\Contracts\HttpClient as ClientContract;
use AhmedEbead\Moyasar\Exceptions\ValidationException;
use AhmedEbead\Moyasar\Invoice;
use AhmedEbead\Moyasar\PaginationResult;
use AhmedEbead\Moyasar\Providers\GuzzleClient;
use AhmedEbead\Moyasar\Search;

class PayoutsService
{
    const URL_PATH = 'payouts';

    /**
     * @var ClientContract
     */
    protected $client;

    public function __construct(ClientContract $client = null)
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
     * Creates a new invoice at Moyasar and return an Invoice object
     *
     * @param array $arguments
     * @return Invoice
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \AhmedEbead\Moyasar\Exceptions\ApiException
     */
    public function create($arguments)
    {
        $arguments = array_merge($this->defaultCreateArguments(), $arguments);
        $this->validateCreateArguments($arguments);

        $response = $this->client->post(self::URL_PATH, $arguments);
        $data = $response->toArray();

        $invoice = Invoice::fromArray($data);
        $invoice->setClient($this->client);
        return $invoice;
    }

    /**
     * Default values for invoice create
     *
     * @return array
     */
    private function defaultCreateArguments()
    {
        return [
            'currency' => 'SAR'
        ];
    }

    /**
     * Validates arguments meant to be used with invoice create
     *
     * @param $arguments
     * @throws ValidationException
     */
    private function validateCreateArguments($arguments)
    {
        $errors = [];

        if (!isset($arguments['amount'])) {
            $errors['amount'][] = 'Amount is required';
        }

        if (isset($arguments['amount']) && (!is_int($arguments['amount']) || $arguments['amount'] <= 0)) {
            $errors['amount'][] = 'Amount must be a positive integer greater than 0';
        }

        if (!isset($arguments['currency'])) {
            $errors['currency'][] = 'Currency is required';
        }

        if (isset($arguments['currency']) && strlen($arguments['currency']) != 3) {
            $errors['currency'][] = 'Currency must be a 3-letter currency ISO code';
        }

        if (!isset($arguments['description']) || strlen(trim($arguments['description'])) == 0) {
            $errors['description'][] = 'A description is required';
        }

        if (count($errors)) {
            throw new ValidationException('Invoice arguments are invalid', $errors);
        }
    }

    /**
     * Fetches an invoice from Moyasar's servers
     *
     * @param string $id
     * @return Invoice
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \AhmedEbead\Moyasar\Exceptions\ApiException
     */
    public function fetch($id)
    {
        $response = $this->client->get(self::URL_PATH . "/$id");
        $data = $response->toArray();
        $invoice = Invoice::fromArray($data);
        $invoice->setClient($this->client);
        return $invoice;
    }

    /**
     * @param Search|array\null $query
     * @return PaginationResult
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \AhmedEbead\Moyasar\Exceptions\ApiException
     */
    public function all($query = null)
    {
        if ($query instanceof Search) {
            $query = $query->toArray();
        }

        $response = $this->client->get(self::URL_PATH, $query);
        $data  =$response->toArray();
        $meta = $data['meta'];

        $invoices = array_map(function ($i) {
            $invoice = Invoice::fromArray($i);
            $invoice->setClient($this->client);
            return $invoice;
        }, $data['invoices']);

        return PaginationResult::fromArray($meta)->setResult($invoices);
    }
}
