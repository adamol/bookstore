<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\StripePaymentGateway;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StripePaymentGatewayTest extends TestCase
{
    public function getPaymentGateway()
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }
}
