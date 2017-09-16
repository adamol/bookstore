<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HashidsInventoryCodeGeneratorTest extends TestCase
{
    /** @test */
    function codes_are_at_least_6_characters_long()
    {
        $generator = new HashidsInventoryCodeGenerator('testsalt1');

        $code = $generator->generateFor(new InventoryItem(['id' => 1]));

        $this->assertTrue(strlen($code) > 6);
    }

    /** @test */
    function codes_only_contain_uppercase_letters()
    {
        $generator = new HashidsInventoryCodeGenerator('testsalt1');

        $code = $generator->generateFor(new InventoryItem(['id' => 1]));

        $this->assertRegexp('/^[A-Z]+$/', $code);
    }

    /** @test */
    function codes_for_the_same_item_are_the_same()
    {
        $generator = new HashidsInventoryCodeGenerator('testsalt1');

        $code1 = $generator->generateFor(new InventoryItem(['id' => 1]));
        $code2 = $generator->generateFor(new InventoryItem(['id' => 1]));

        $this->assertEquals($code1, $code2);
    }

    /** @test */
    function codes_for_different_items_are_different()
    {
        $generator = new HashidsInventoryCodeGenerator('testsalt1');

        $code1 = $generator->generateFor(new InventoryItem(['id' => 1]));
        $code2 = $generator->generateFor(new InventoryItem(['id' => 2]));

        $this->assertNotEquals($code1, $code2);
    }

    /** @test */
    function codes_generated_with_different_salts_are_different()
    {
        $generator1 = new HashidsInventoryCodeGenerator('testsalt1');
        $generator2 = new HashidsInventoryCodeGenerator('testsalt2');

        $code1 = $generator1->generateFor(new InventoryItem(['id' => 1]));
        $code2 = $generator2->generateFor(new InventoryItem(['id' => 1]));

        $this->assertNotEquals($code1, $code2);
    }
}
