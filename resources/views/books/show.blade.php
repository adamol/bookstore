@extends('layouts.app')

@section('content')
<h1>{{ $book->title }}</h1>

<p>written by: {{ $book->author->name }}, category: {{ $book->category->name }}</p>
<p>{{ $book->description }}</p>
<p>{{ $book->formatted_inventory_quantity }}</p>
<p>{{ $book->formatted_price }}£</p>
<form method="POST" action="/cart">
    {{ csrf_field() }}
    <input type="text" name="quantity">
    <input type="hidden" name="book_id" value="{{ $book->id }}">
    <button type="submit">Add to cart</button>
</form>
@endsection
