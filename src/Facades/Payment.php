<?php

namespace AhmedEbead\Moyasar\Facades;

use Illuminate\Support\Facades\Facade;
use AhmedEbead\Moyasar\Providers\PaymentService;

/**
 * @method static \AhmedEbead\Moyasar\Facades\Payment fetch($id)
 * @method static \AhmedEbead\Moyasar\PaginationResult all($query = null)
 *
 * @see \AhmedEbead\Moyasar\Providers\PaymentService
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
