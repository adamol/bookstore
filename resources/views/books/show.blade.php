@extends('layouts.app')

@section('content')
<h1>{{ $book->title }}</h1>

<p>written by: {{ $book->author_names }}, category: {{ $book->category_names }}</p>
<p>{{ $book->description }}</p>
@endsection
