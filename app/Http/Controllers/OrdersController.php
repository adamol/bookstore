<?php

namespace App\Http\Controllers;

use Mail;
use App\Facades\InventoryCode;
use App\Mail\OrderConfirmationEmail;
use App\Facades\OrderConfirmationNumber;
use App\Exceptions\NotEnoughInventoryException;
use App\Exceptions\TokenMismatchException;
use App\Exceptions\EmptyCartException;
use App\Billing\PaymentGateway;
use Illuminate\Http\Request;
use App\ShoppingCart;
use App\Order;

class OrdersController extends Controller
{
    public function show($confirmationNumber)
    {
        $order = Order::where('confirmation_number', $confirmationNumber)->first();

        $order->load('inventoryItems.book.author');

        return view('orders.show', compact('order'));
    }

    public function store(Request $request, PaymentGateway $paymentGateway, ShoppingCart $cart)
    {
        $this->validate($request, [
            'payment_token' => 'required',
            'email' => 'required|email'
        ]);

        try {
            $reservation = $cart->reserveFor($request->email);

            $order = $reservation->complete($paymentGateway, $request->payment_token);

            Mail::to($order->email)->send(new OrderConfirmationEmail($order));

            if ($request->expectsJson()) {
                return response()->json($order, 201);
            }

            return redirect("orders/{$order->confirmation_number}");
        } catch (EmptyCartException $e) {
            return response()->json(['cart' => 'Cannot place an order for an empty cart.'], 422);
        } catch (NotEnoughInventoryException $e) {
            return response()->json(['inventory' => 'Cannot place an order an item quantity which exceeds the inventory limit.'], 422);
        } catch (TokenMismatchException $e) {
            return response()->json(['payment_token' => 'Could not place an order with the given payment token.'], 422);
        }
    }
}
