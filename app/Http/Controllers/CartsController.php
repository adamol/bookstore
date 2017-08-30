<?php

namespace App\Http\Controllers;

use App\Book;
use App\ShoppingCart;
use App\InventoryItem;
use Illuminate\Http\Request;
use App\Exceptions\NotEnoughInventory;

class CartsController extends Controller
{
    public function show()
    {
        return ShoppingCart::get();
    }

    public function store(Request $request)
    {
        try {
            ShoppingCart::add($request->book_id, $request->quantity);
        } catch (NotEnoughInventory $e) {

        }
    }
}
