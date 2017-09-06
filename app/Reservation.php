<?php

namespace App;

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
}
