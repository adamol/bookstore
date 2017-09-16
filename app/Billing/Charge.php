<?php

namespace App\Billing;

class Charge
{
    protected $amount;

    protected $cardLastFour;

    public function __construct($amount, $cardLastFour)
    {
        $this->amount = $amount;
        $this->cardLastFour = $cardLastFour;
    }

    public function amount()
    {
        return $this->amount;
    }

    public function cardLastFour()
    {
        return $this->cardLastFour;
    }
}
