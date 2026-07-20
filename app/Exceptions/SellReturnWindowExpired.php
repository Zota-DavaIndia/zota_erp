<?php

namespace App\Exceptions;

use Exception;

/**
 * Thrown when a customer attempts to return a sale after the store's
 * configurable return window (business.sell_return_period_days) has
 * elapsed. Carries a user-facing message that the controllers surface
 * directly to the cashier / API caller.
 */
class SellReturnWindowExpired extends Exception
{
    //
}
