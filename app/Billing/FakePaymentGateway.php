<?php

namespace App\Billing;

use App\Exceptions\TokenMismatchException;

class FakePaymentGateway implements PaymentGateway
{
    protected $charges;

    public function __construct()
    {
        $this->charges = [];
    }

    public function charge($amount, $token)
    {
        if ($token !== 'TESTTOKEN1234') {
            throw new TokenMismatchException;
        }
        $this->charges[] = $amount;

        return $amount;
    }

    public function charges()
    {
        return $this->charges;
    }
}
