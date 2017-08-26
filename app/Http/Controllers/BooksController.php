<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Book;

class BooksController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('category')) {
            $books = Book::where('category', $request->category)->get();
        } elseif ($request->has('author')) {
            $author = ucwords(str_replace('_', ' ', $request->author));
            $books = Book::where('author', $author)->get();
        } else {
            $books = Book::all();
        }

        return view('books.index', compact('books'));
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);

        return view('books.show', compact('book'));
    }
}
