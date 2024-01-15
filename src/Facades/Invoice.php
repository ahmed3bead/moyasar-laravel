<?php

namespace AhmedEbead\Moyasar\Facades;

use Illuminate\Support\Facades\Facade;
use AhmedEbead\Moyasar\Providers\InvoiceService;

/**
 * @method static \AhmedEbead\Moyasar\Invoice create($arguments)
 * @method static \AhmedEbead\Moyasar\Invoice fetch($id)
 * @method static \AhmedEbead\Moyasar\PaginationResult all($query = null)
 *
 * @see \AhmedEbead\Moyasar\Providers\InvoiceService
 *
 * Class Invoice
 * @package Moyasar\Facades
 */
class Invoice extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return InvoiceService::class;
    }
}
