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

    public static function byKey($key)
    {
        $name = ucwords(str_replace('_', ' ', $key));

        return Author::where('name', $name)->first();
    }
}
