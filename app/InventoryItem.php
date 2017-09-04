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
    }
}
