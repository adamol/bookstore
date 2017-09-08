<?php

namespace App;

use App\Exceptions\EmptyCartException;

class ShoppingCart
{
    public function reserveFor($email)
    {
        $cartItems = self::get();

        if ($cartItems->isEmpty()) {
            throw new EmptyCartException;
        }

        foreach ($cartItems as $item) {
            Book::find($item['book_id'])->assertEnoughInventory($item['quantity']);
        }

        $items = $cartItems->map(function($item) {
            return InventoryItem::reserveFor($item['book_id'], $item['quantity']);
        });

        return new Reservation($items->flatten(), $email);
    }

    public function add($bookId, $quantity)
    {
        Book::find($bookId)->assertEnoughInventory($quantity);

        session()->push('cart.books', [
            'book_id' =>$bookId,
            'quantity' => $quantity
        ]);
    }

    public function get()
    {
        return collect(session()->get('cart.books'));
    }
}
