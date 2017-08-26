<?php

use Illuminate\Database\Seeder;
use App\Book;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $authors = ['John Doe', 'Jane Doe'];
        $categories = ['fantasi', 'thriller'];

        foreach ($authors as $author) {
            foreach ($categories as $category) {
                factory(Book::class)->create([
                    'author' => $author,
                    'category' => $category
                ]);

            }
        }
    }
}
