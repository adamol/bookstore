<?php

namespace App;

use App\Facades\OrderConfirmationNumber;

class Reservation
{
    protected $items;

    protected $email;

    public function __construct($items, $email)
    {
        $this->items = $items;
        $this->email = $email;
    }

    public function amount()
    {
        return $this->items->sum('price');
    }

    public function email()
    {
        return $this->email;
    }

    public function items()
    {
        return $this->items;
    }

    public function complete($paymentGateway, $token)
    {
        $charge = $paymentGateway->charge($this->amount(), $token);

        $order = Order::create([
            'amount'              => $charge->amount(),
            'email'               => $this->email(),
            'confirmation_number' => OrderConfirmationNumber::generate(),
            'card_last_four'      => $charge->cardLastFour()
        ]);

        $this->items()->each->claimFor($order);

        return $order;
    }
}
