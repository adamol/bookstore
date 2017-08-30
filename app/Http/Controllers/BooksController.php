<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Author;
use App\Book;

class BooksController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('category')) {
            $category = Category::where('name', $request->category)->firstOrFail();
            $books = $category->books()->get();
        } elseif ($request->has('author')) {
            $author = ucwords(str_replace('_', ' ', $request->author));
            $author = Author::where('name', $author)->firstOrFail();
            $books = $author->books()->get();
        } else {
            $books = Book::all();
        }
        $books->load('authors', 'categories');

        return view('books.index', compact('books'));
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);

        return view('books.show', compact('book'));
    }
}
