
# Moyasar Laravel

Moyasar PHP wrapper library

## Documentation

See the [PHP API docs](https://moyasar.com/docs/api/?php)

## Requirements

- PHP 5.6.0
- guzzlehttp/guzzle: ^6.3.0
- laravel/framework: ^9.0

#### Notes

- Please note that starting from version `1.0.0` the library was rewritten with breaking changes, so please do not update
  unless you need the new version. If you are new, it is recommended to use the new version.
- To use the PHP stream handler, allow_url_fopen must be enabled in your system's php.ini.
- To use the cURL handler, you must have a recent version of cURL >= 7.19.4 compiled with OpenSSL and zlib.
- Please note that in version `0.5.0` the library name has been changed from `moyasar-php` to `moyasar`

## Installation

You can install it via [composer](https://getcomposer.org/)

    $ composer require ahmed3bead/moyasar-laravel

## Usage


After that, moyasar services need to be configured, so let us publish the configuration file:

    $ php artisan vendor:publish --provider="AhmedEbead\Moyasar\Providers\LaravelServiceProvider"

Now edit `config/moyasar.php` and add your API key, by default the API key is read from
an environment variable called `MOYASAR_API_PUBLISHABLE_KEY`, thus `.env` can be used to add the key.

```env
MOYASAR_API_PUBLISHABLE_KEY=<Your_Key>
MOYASAR_API_SECRET_KEY=<Your_Key>
FINISH_PAYMENT_URL=<url-here>
```

If everything goes to plan, you should be able to get `PaymentService` and `InvoiceService`
from laravel service container by simply called `app` helper function

```php
app(PaymentService::class)
```

```php
app(InvoiceService::class)
```

Or inside your controller, you can simply type-hint one of the services in the constructor:

```php
public function __construct(PaymentService $paymentService)
{
    $this->paymentService = $paymentService;
}
```

---

#### Payment

Note: Moyasar does not allow creating payments using the API (with some exceptions), instead you can use
the [payment form](https://moyasar.com/docs/payments/create-payment/mpf/). That is why, wrapper libraries does not support it.

---

To fetch a payment, just simply do the following:

```php
$paymentService = new \AhmedEbead\Moyasar\Providers\PaymentService();
```
##### Create Payment

```php
$data = [
            "amount" => 100,
            "currency" => "SAR",
            "description" => "Payment for order #",
            "callback_url" => "https://example.com/thankyou",
            "source" => [
                "type" => "creditcard",
                "name" => "Mohammed Ali",
                "number" => "4111111111111111",
                "cvc" => "123",
                "month" => "12",
                "year" => "26"
            ]
        ];
        $payment = $paymentService->create($data);
```

##### Fetch Payment
```php
$payment = $paymentService->fetch('ae5e8c6a-1622-45a5-b7ca-9ead69be722e');
```

##### list all Payment
```php
$payment = $paymentService->all();
```

An instance of `Payment` will be returned, that has the data in addition to being able
to perform operations like `update`, `refund`, `capture`, `void` on that payment instance,
which we will get back to later.

---

To list payments associated with your account, simply do the following:

```php
$paymentService = new \AhmedEbead\Moyasar\Providers\PaymentService();

$paginationResult = $paymentService->all();

$payments = $paginationResult->result;
```

The `all` method will return an instance of `PaginationResult` this contains meta data
about our result, like `currentPage`, `totalPages` etc...

To get the payments from this object, we just read the `result` property of that object.

---

The `all` method accepts an instance of `Search` or an array, this allows us to filter
results and move along pages. It is quite simple to use:

```php
$search = \AhmedEbead\Moyasar\Search::query()->status('paid')->page(2);

$paginationResult = $paymentService->all($search);
```

The following methods are supported:

- `id($id)`
- `status($status)`
- `source($source)`
- `page($page)`
- `createdAfter($date)`
- `createdBefore($date)`

---

Once we fetch the desired payment, we can either `update` the description, `refund` it,
`capture` it, or `void` it.

```php
$payment->update('new description here');

// OR

$payment->refund(1000); // 10.00 SAR

// OR

$payment->capture(1000);

// OR

$payment->void();
```

#### Invoice

For invoices, fetching and listing them is the same as payments, instead we use `InvoiceService`.

Although, we can use the API to create a new invoice, by doing the following:

```php
$invoiceService = new \AhmedEbead\Moyasar\Providers\InvoiceService();

$invoiceService->create([
    'amount' => 1000000, // 10000.00 SAR
    'currency' => 'SAR',
    'description' => 'iPhone XII Purchase',
    'callback_url' => 'http://www.example.com/invoice-status-changed', // Optional
    'expired_at' => '2020-01-20' // Optional
]);
```

---

With an instance of `Invoice`, we can either `update`, or `cancel` a given instance.

```php
$invoice->update([
    'amount' => 900000, // 9000.00 SAR
    'currency' => 'SAR',
    'description' => 'iPhone XII Purchase (Updated)',
    'callback_url' => 'http://www.example.com/invoice-status-changed', // Optional
    'expired_at' => '2020-01-25' // Optional
]);

// OR

$invoice->cancel();
```


Or if you want a quick way to use these services, you can use the `Payment` and `Invoice` facades:

- `AhmedEbead\Moyasar\Facades\Payment`
- `AhmedEbead\Moyasar\Facades\Invoice`

For example:

```php
$payment = \Moyasar\Facades\Payment::fetch('id');
```

## TODO
- Payout Payment
- Token Payment
- Webhooks

## License

The package is available as open source under the terms of the [MIT License](http://opensource.org/licenses/MIT).


