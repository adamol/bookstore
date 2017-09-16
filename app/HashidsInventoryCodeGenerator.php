<?php

namespace App;

use Hashids\Hashids;

class HashidsInventoryCodeGenerator
{
    public function __construct($salt)
    {
        $this->hashids = new Hashids($salt, 6, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    public function generateFor($inventoryItem)
    {
        $this->hashids->encode($inventoryItem->id);
    }
}
