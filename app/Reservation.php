<?php

namespace App;

class Reservation
{
    protected $books;

    protected $email;

    public function __construct($books, $email)
    {
        $this->books = $books;
        $this->email = $email;
    }

    public function amount()
    {
        return $this->books->sum('price');
    }

    public function email()
    {
        return $this->email;
    }
}
