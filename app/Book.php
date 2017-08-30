<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = [];

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
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
}
