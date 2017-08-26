@extends('layouts.app')

@section('content')
<h1>{{ $book->title }}</h1>

<p>written by: {{ $book->author }}, category: {{ $book->category }}</p>
<p>{{ $book->description }}</p>
@endsection
