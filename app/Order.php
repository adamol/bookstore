<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function toArray()
    {
        return [
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'email' => 'john@example.com',
            'amount' => 3500,
            'inventory_items' => $this->inventoryItems->map(function($item) {
                return ['code' => $item->code];
            })
        ];
    }
}
