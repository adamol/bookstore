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
        } else {
            $books = Book::all();
        }

        return $books->pluck('title');
    }

    public function show($id)
    {
        $book = Book::findOrFail($id);

        return $book;
    }
}
