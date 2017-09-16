<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\InventoryCodeGenerator;

class InventoryCode extends Facade
{
    protected static function getFacadeAccessor()
    {
        return InventoryCodeGenerator::class;
    }
}

