<?php

namespace App;

class ShoppingCart
{
    public static function add($bookId, $quantity)
    {
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
