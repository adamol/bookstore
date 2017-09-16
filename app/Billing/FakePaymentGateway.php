<?php

namespace App\Billing;

use App\Exceptions\TokenMismatchException;

class FakePaymentGateway implements PaymentGateway
{
    const TEST_CARD_NUMBER = '4242424242424242';

    protected $charges;

    protected $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect([]);

        $this->tokens = collect([]);

        $this->beforeFirstChargeCallback = null;
    }

    public function validTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        $token = 'fake-tok_'.str_random(24);
        $this->tokens[$token] = $cardNumber;
        return $token;
    }

    public function newChargesDuring(callable $callback)
    {
        $chargesCount = $this->charges->count();

        $callback($this);

        return $this->charges->slice($chargesCount)->reverse()->values();
    }

    public function charge($amount, $token)
    {
        if ($this->beforeFirstChargeCallback) {
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }
        if (! $this->tokens->has($token)) {
            throw new TokenMismatchException;
        }

        $charge = new Charge(
            $amount,
            substr($this->tokens[$token], -4)
        );

        $this->charges[] = $charge;

        return $charge;
    }

    public function beforeFirstCharge(callable $callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }

    public function totalCharges()
    {
        return $this->charges->map->amount()->sum();
    }
}
