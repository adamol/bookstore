<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $guarded = [];

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public static function keyToName($key)
    {
        return ucwords(str_replace('_', ' ', $key));
    }
}
