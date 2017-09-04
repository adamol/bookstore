<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\InventoryItem;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InventoryItemTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function an_inventory_item_can_be_reserved()
    {
        $item = factory(InventoryItem::class)->create();

        $item->reserve();

        $this->assertNotNull($item->fresh()->reserved_at);
    }
}
