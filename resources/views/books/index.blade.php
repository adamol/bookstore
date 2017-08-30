@extends('layouts.app')

@section('content')
<h1>Books Listing</h1>

<ul>
    @foreach ($books as $book)
        <li><a href="books/{{ $book->id }}">{{ $book->title }}</a> by {{ $book->author_names }}</li>
    @endforeach
</ul>
@endsection
