<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InventoryItem extends Model
{
    protected $guarded = [];

    public function reserve()
    {
        $this->update(['reserved_at' => Carbon::now()]);

        return $this;
    }

    public static function reserveFor($book, $quantity)
    {
        return InventoryItem::where('book_id', $book->id)
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
}
