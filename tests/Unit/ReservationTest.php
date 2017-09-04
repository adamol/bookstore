<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Reservation;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReservationTest extends TestCase
{
    /** @test */
    function amount_returns_the_sum_of_the_prices_of_the_books()
    {
        $books = collect([
            (object) ['price' => 1000],
            (object) ['price' => 2000],
            (object) ['price' => 500],
            (object) ['price' => 1500],
        ]);

        $reservation = new Reservation($books, 'john@example.com');

        $this->assertEquals(5000, $reservation->amount());
    }

    /** @test */
    function email_returns_the_email_for_the_reservation()
    {
        $reservation = new Reservation(collect([]), 'john@example.com');

        $this->assertEquals('john@example.com', $reservation->email());
    }
}
