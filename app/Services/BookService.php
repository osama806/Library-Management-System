<?php

namespace App\Services;

use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookService
{
    use ResponseTrait;

    /**
     * Display a listing of the books.
     * @param array $data
     * @return array
     */
    public function index(array $data)
    {
        $booksQuery = Book::query();

        $booksQuery->author($data['author'] ?? null);

        $booksQuery->availableBooks($data['available_books'] ?? null);

        $booksQuery->category($data['category'] ?? null);

        // Get books and ratings related to each book
        $books = $booksQuery->with(['rating.user'])->get();

        return ['status' => true, 'books' => BookResource::collection($books)];
    }

    /**
     * Display the specified book.
     * @param \App\Models\Book $book
     * @return array
     */
    public function show(Book $book)
    {
        return ['status'    =>  true, 'book'    =>  new BookResource($book)];
    }

    /**
     * Store a newly created resource in storage.
     * @param array $data
     * @throws \Exception
     * @return boolean[]
     */
    public function store(array $data)
    {
        $date = Carbon::now();
        try {
            Book::create([
                "title"         =>      $data['title'],
                "author"        =>      $data['author'],
                "description"   =>      $data['description'],
                "published_at"  =>      $date,
                "category_id"   =>      $data['category_id']
            ]);
            return ['status'    =>  true];
        } catch (Exception $e) {
            Log::error('Error creating book: ' . $e->getMessage());
            return ['status' => false, 'msg' => 'There is an error on the server', 'code' => 500];
        }
    }

    /**
     * Update the specified book in storage.
     * @param array $data
     * @param \App\Models\Book $book
     * @return array
     */
    public function update(array $data, Book $book)
    {
        $filteredData = array_filter($data, function ($value) {
            return !is_null($value) && trim($value) !== '';
        });

        if (empty($filteredData)) {
            return [
                'status'        =>      false,
                'msg'           =>      'Not Found Any Data in Request',
                'code'          =>      404
            ];
        }

        $book->update($filteredData);
        return ['status'    =>  true];
    }
}
