<?php

namespace App\Http\Controllers;

use App\Book;
use App\ShoppingCart;
use Illuminate\Http\Request;

class CartsController extends Controller
{
    public function store(Request $request)
    {
        ShoppingCart::add($request->book_id, $request->quantity);
        # session()->push('cart.books', [
        #     'book_id'  => $request->book_id,
        #     'quantity' => $request->quantity
        # ]);
    }
}
