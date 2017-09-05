<?php

namespace App;

use Carbon\Carbon;
use App\Exceptions\NotEnoughInventoryItems;

class Inventory
{
    public static function reserveBooks($books, $email)
    {
        if ($books->isEmpty()) {
            throw new EmptyCartException;
        }

        $books->each(function($book) {
            if ($book->inventory_quantity < $book->quantity) {
                throw new NotEnoughInventoryItems;
            }
        });

        $books->each(function($book) {
            Inventory::findFor($book, $book->quantity)->each->reserve();
        });

        return new Reservation($books, $email);
    }

    public static function findFor($book, $quantity)
    {
        return InventoryItem::where('book_id', $book->id)->take($quantity)->get();
    }
}
