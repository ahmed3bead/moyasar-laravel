<?php

namespace AhmedEbead\Moyasar\Facades;

use AhmedEbead\Moyasar\Services\PaymentService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \AhmedEbead\Moyasar\Facades\Payment fetch($id)
 * @method static \AhmedEbead\Moyasar\PaginationResult all($query = null)
 *
 * @see \AhmedEbead\Moyasar\Services\PaymentService
 *
 * Class Payment
 * @package Moyasar\Facades
 */
class Payment extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PaymentService::class;
    }
}
