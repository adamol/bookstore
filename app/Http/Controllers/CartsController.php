<?php

namespace App\Http\Controllers;

use App\Book;
use App\ShoppingCart;
use App\InventoryItem;
use Illuminate\Http\Request;
use App\Exceptions\NotEnoughInventory;

class CartsController extends Controller
{
    public function show(ShoppingCart $cart)
    {
        return view('cart.show', ['books' => $cart->get()]);
    }

    public function store(Request $request, ShoppingCart $cart)
    {
        try {
            $cart->add($request->book_id, $request->quantity);
        } catch (NotEnoughInventory $e) {

        }

        return redirect()->back();
    }
}
