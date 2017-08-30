<?php

namespace App;

use App\Exceptions\NotEnoughInventory;

class ShoppingCart
{
    public static function add($bookId, $quantity)
    {
        if (InventoryItem::where('book_id', $bookId)->count() < $quantity) {
            throw new NotEnoughInventory;
        }

        session()->push('cart.books', [
            'book_id'  => $bookId,
            'quantity' => $quantity
        ]);
    }

    public static function get()
    {
        return collect(session()->get('cart.books'))->map(function($data) {
            $book = Book::findOrFail($data['book_id']);
            $book->quantity = $data['quantity'];
            return $book;
        });
    }
}
