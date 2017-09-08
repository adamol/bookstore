<?php

namespace App\Filters;

use Illuminate\Http\Request;
use App\Category;

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

        foreach ($this->filters as $filter) {
            if ($this->request->has($filter) && method_exists($this, $filter)) {
                $this->$filter();
            }
        }

        return $this->query;
    }

    protected function author()
    {
        $this->query->join('author_book', 'books.id', '=', 'author_book.book_id');

        $this->query->join('authors', 'author_book.author_id', '=', 'authors.id');

        $author = ucwords(str_replace('_', ' ', $this->request->author));

        $this->query->where('name', $author);
    }

    protected function category()
    {
        $this->query->join('book_category', 'books.id', '=', 'book_category.book_id');

        $this->query->join('categories', 'book_category.category_id', '=', 'categories.id');

        $this->query->where('name', $this->request->category);
    }

    protected function price()
    {

    }
}
