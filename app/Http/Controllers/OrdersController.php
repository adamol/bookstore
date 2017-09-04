<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use Illuminate\Http\Request;
use App\ShoppingCart;

class OrdersController extends Controller
{
    public function store(Request $request, PaymentGateway $paymentGateway)
    {
        $books = ShoppingCart::get();

        try {
            $reservation = Inventory::reserveBooks($books);

            $amount = $paymentGateway->charge($reservation->amount);

            $order = Order::create([
                'amount' => $amount,
                'email' => $request->email,
                'confirmation_number' => OrderConfirmationNumber::generate()
            ]);

            $order->inventoryItems()->attach($reservation->inventoryItems());
        } catch (NotEnoughInventoryItems $e) {

        } catch (InvalidPayment $e) {

        }
    }
}
