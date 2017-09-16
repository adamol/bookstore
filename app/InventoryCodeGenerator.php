<?php

namespace App;

interface InventoryCodeGenerator
{
    public function generateFor($inventoryItem);
}
