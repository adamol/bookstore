<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotEnoughInventoryException;
use App\Filters\BookFilters;

class Book extends Model
{
    protected $guarded = [];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assertEnoughInventory($quantity)
    {
        if ($this->inventory_quantity < $quantity) {
            throw new NotEnoughInventoryException;
        }
    }

    public function scopeApplyFilters($query, BookFilters $filters)
    {
        return $filters->applyTo($query);
    }

    public function getFormattedPriceAttribute()
    {
        return number_format($this->price / 100, 2);
    }

    public function getFormattedInventoryQuantityAttribute()
    {
        $inventoryQuantity = $this->inventory_quantity;

        return $inventoryQuantity == 0
            ? 'out of stock'
            : $inventoryQuantity . ' in stock';
    }

    public function getInventoryQuantityAttribute()
    {
        return InventoryItem::where('book_id', $this->id)
            ->whereNull('reserved_at')
            ->count();
    }

    public function addInventory($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            InventoryItem::create(['book_id' => $this->id]);
        }

        return $this;
    }
}
