<?php

namespace App;

use App\Exceptions\NotEnoughInventoryException;
use App\Exceptions\EmptyCartException;

class ShoppingCart
{
    public static function reserveFor($email)
    {
        $books = self::get();

        if ($books->isEmpty()) {
            throw new EmptyCartException;
        }

        $books->each->assertEnoughInventory();

        $items = $books->map(function($book) {
            return InventoryItem::reserveFor($book, $book->quantity);
        })->flatten();

        return new Reservation($items, $email);
    }

    public static function add($bookId, $quantity)
    {
        if (InventoryItem::where('book_id', $bookId)->count() < $quantity) {
            throw new NotEnoughInventoryException;
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
