@extends('layouts.app')

@section('content')
<h1>{{ $book->title }}</h1>

<p>written by: {{ $book->author_names }}, category: {{ $book->category_names }}</p>
<p>{{ $book->description }}</p>

<form method="POST" action="/cart">
    {{ csrf_field() }}
    <input type="text" name="quantity">
    <input type="hidden" name="book_id" value="{{ $book->id }}">
    <button type="submit">Add to cart</button>
</form>
@endsection
