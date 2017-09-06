<?php

namespace App\Http\Controllers;

use App\Exceptions\TokenMismatchException;
use App\Exceptions\NotEnoughInventoryException;
use App\Billing\PaymentGateway;
use Illuminate\Http\Request;
use App\ShoppingCart;
use App\Inventory;
use App\Order;

class OrdersController extends Controller
{
    public function store(Request $request, PaymentGateway $paymentGateway)
    {
        $this->validate($request, [
            'payment_token' => 'required',
            'email' => 'required|email'
        ]);

        try {
            $reservation = ShoppingCart::reserveFor($request->email);

            $amount = $paymentGateway->charge(
                $reservation->amount(), $request->payment_token
            );

            $order = Order::create([
                'amount' => $amount,
                'email' => $reservation->email(),
                # 'confirmation_number' => OrderConfirmationNumber::generate(),
                # 'card_last_four' => $charge->cardLastFour()
            ]);

            foreach ($reservation->items() as $item) {
                $order->inventoryItems()->save($item);
            }
        } catch (EmptyCartException $e) {
            return response()->json(['cart' => 'Cannot place an order for an empty cart.'], 422);
        } catch (NotEnoughInventoryException $e) {

        } catch (TokenMismatchException $e) {
            return response()->json(['payment_token' => 'Could not place an order with the given payment token.'], 422);
        }
    }
}
