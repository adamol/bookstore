<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaymentGatewayContractTest extends TestCase
{
    abstract public function getPaymentGateway();

    /** @test */
    function charges_with_valid_payment_token_pass()
    {
        $paymentGateway = $this->getPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(1000, $paymentGateway->validTestToken());
            $paymentGateway->charge(1500, $paymentGateway->validTestToken());
        });

        $this->assertEquals(2500, $newCharges->map->amount()->sum());
    }

    /** @test */
    function can_get_details_about_a_charge()
    {
        $paymentGateway = $this->getPaymentGateway();

        $charge = $paymentGateway->charge(2500, $paymentGateway->validTestToken($paymentGateway::TEST_CARD_NUMBER));

        $this->assertEquals(substr($paymentGateway::TEST_CARD_NUMBER, -4), $charge->cardLastFour());
        $this->assertEquals(2500, $charge->amount());
    }

    /** @test */
    function charges_with_invalid_payment_token_fail()
    {
        $paymentGateway = $this->getPaymentGateway();

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            try {
                $paymentGateway->charge(1000, 'invalid-payment-token');

                $this->fail('Payment succeeded dispite invalid payment token.');
            } catch (TokenMismatchException $e) {
            }
        });

        $this->assertEmpty($newCharges);
    }
}
