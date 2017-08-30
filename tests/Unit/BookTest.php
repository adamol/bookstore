<?php

namespace Tests\Unit;

use App\Book;
use App\Author;
use App\Category;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BookTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_format_multiple_author_names()
    {
        $book = factory(Book::class)->create();
        $book->authors()->attach(
            factory(Author::class)->create(['name' => 'John Doe'])
        );
        $book->authors()->attach(
            factory(Author::class)->create(['name' => 'Jane Doe'])
        );

        $this->assertEquals('John Doe, Jane Doe', $book->author_names);
    }

    /** @test */
    public function can_format_multiple_category_names()
    {
        $book = factory(Book::class)->create();
        $book->categories()->attach(
            factory(Category::class)->create(['name' => 'fantasi'])
        );
        $book->categories()->attach(
            factory(Category::class)->create(['name' => 'thriller'])
        );

        $this->assertEquals('Fantasi, Thriller', $book->category_names);
    }
}
