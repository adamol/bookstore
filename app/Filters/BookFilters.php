<?php

namespace App\Filters;

use Illuminate\Http\Request;
use App\Category;
use App\Author;

class BookFilters
{
    protected $filters = [
        'author', 'category', 'price'
    ];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function applyTo($query)
    {
        $this->query = $query;

        foreach ($this->getFilters() as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }

        return $this->query;
    }

    protected function getFilters()
    {
        return $this->request->intersect($this->filters);
    }

    protected function author($author)
    {
        $author = Author::byKey($author);

        $this->query->where('author_id', $author->id);
    }

    protected function category($category)
    {
        $category = Category::where('name', $category)->first();

        $this->query->where('category_id', $category->id);
    }
}
