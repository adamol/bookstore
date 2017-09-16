<?php


Route::get('books', 'BooksController@index');
Route::get('books/{id}', 'BooksController@show');

Route::get('cart', 'CartsController@show');
Route::post('cart', 'CartsController@store');

Route::get('orders/{confirmationNumber}', 'OrdersController@show');
Route::post('orders', 'OrdersController@store');
