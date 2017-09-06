<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotEnoughInventoryException;

class Book extends Model
{
    protected $guarded = [];

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function assertEnoughInventory()
    {
        if ($this->inventory_quantity < $this->quantity) {
            throw new NotEnoughInventoryException;
        }
    }

    public function getAuthorNamesAttribute()
    {
        return rtrim(implode(', ', $this->authors->map(function($author) {
            return $author->name;
        })->toArray()));
    }

    public function getCategoryNamesAttribute()
    {
        return rtrim(implode(', ', $this->categories->map(function($category) {
            return ucfirst($category->name);
        })->toArray()));
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
        return InventoryItem::where('book_id', $this->id)->whereNull('reserved_at')->count();
    }

    public function addInventory($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            InventoryItem::create(['book_id' => $this->id]);
        }

        return $this;
    }
}
