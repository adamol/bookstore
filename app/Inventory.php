<?php

namespace App;

use Carbon\Carbon;

class Inventory
{
    public static function reserveBooks($books)
    {
        $books->each(function($book) {
            if ($book->inventory_quantity < $book->quantity) {
                throw new \Exception('Too few items in inventory');
            }
        });

        $books->each(function($book) {
            Inventory::findFor($book, $book->quantity)->each->reserve();
        });
    }

    public static function findFor($book, $quantity)
    {
        return InventoryItem::where('book_id', $book->id)->take($quantity)->get();
    }
}
