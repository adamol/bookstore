@extends('layouts.app')

@section('content')
<h1>Shopping Cart</h1>

<ul>
    @foreach($books as $book)
        <li>{{ $book->title }} x {{ $book->quantity }}</li>
    @endforeach
</ul>
@endsection
