<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Facades\InventoryCode;
use Carbon\Carbon;

class InventoryItem extends Model
{
    protected $guarded = [];

    public function reserve()
    {
        $this->update(['reserved_at' => Carbon::now()]);

        return $this;
    }

    public function scopeAvailable($query)
    {
        return $query->whereNull('reserved_at')->whereNull('order_id');
    }

    public static function reserveFor($bookId, $quantity)
    {
        return InventoryItem::where('book_id', $bookId)
            ->whereNull('reserved_at')
            ->take($quantity)
            ->get()
            ->each
            ->reserve();
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function getPriceAttribute()
    {
        return $this->book->price;
    }

    public function claimFor($order)
    {
        $this->code = InventoryCode::generateFor($order);
        $order->inventoryItems()->save($this);
    }
}
