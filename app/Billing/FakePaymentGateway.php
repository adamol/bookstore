<?php

namespace App\Billing;

class FakePaymentGateway implements PaymentGateway
{
    protected $charges;

    public function __construct()
    {
        $this->charges = [];
    }

    public function charge($amount)
    {
        $this->charges[] = $amount;
    }

    public function charges()
    {
        return $this->charges;
    }
}
