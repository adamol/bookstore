<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Filters\BookFilters;
use App\Category;
use App\Author;
use App\Book;

class BooksController extends Controller
{
    public function index(BookFilters $filters)
    {
        $books = Book::applyFilters($filters)->get();

        return view('books.index', compact('books'));
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);

        return view('books.show', compact('book'));
    }
}
