<?php

use Illuminate\Database\Seeder;
use App\InventoryItem;
use App\Category;
use App\Author;
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
        $john = factory(Author::class)->create(['name' => 'John Doe']);
        $jane = factory(Author::class)->create(['name' => 'Jane Doe']);

        $fantasi  = factory(Category::class)->create(['name' => 'fantasi']);
        $thriller = factory(Category::class)->create(['name' => 'thriller']);

        $bookA = factory(Book::class)->create(['title' => 'Book A']);
        $bookA->authors()->attach($jane);
        $bookA->categories()->attach($fantasi);
        factory(InventoryItem::class, 5)->create(['book_id' => $bookA]);

        $bookB = factory(Book::class)->create(['title' => 'Book B']);
        $bookB->authors()->attach($john);
        $bookB->categories()->attach($thriller);
        factory(InventoryItem::class, 5)->create(['book_id' => $bookB]);

        $bookC = factory(Book::class)->create(['title' => 'Book C']);
        $bookC->authors()->attach($jane);
        $bookC->authors()->attach($john);
        $bookC->categories()->attach($fantasi);
        $bookC->categories()->attach($thriller);
        factory(InventoryItem::class, 5)->create(['book_id' => $bookC]);

    }
}
