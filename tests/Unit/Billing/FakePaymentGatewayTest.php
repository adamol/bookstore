<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Exceptions\TokenMismatchException;

class FakePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTest;

    public function getPaymentGateway()
    {
        return new FakePaymentGateway;
    }
}
