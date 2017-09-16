@extends('layouts.app')

@section('content')

{{ $order->confirmation_number }}
**** **** **** {{ $order->card_last_four }}

@foreach ($order->inventoryItems as $item)
    {{ $item->book->author->name }}
    {{ $item->book->title }}
    {{ $item->code }}
@endforeach

@endsection
