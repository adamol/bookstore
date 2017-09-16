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
        $this->query
            ->join('authors', 'books.author_id', '=', 'authors.id')
            ->where('authors.name', Author::keyToName($author));
    }

    protected function category($category)
    {
        $this->query
            ->join('book_category', 'books.id', '=', 'book_category.book_id')
            ->join('categories', 'book_category.category_id', '=', 'categories.id')
            ->where('categories.name', $category);
    }
}
