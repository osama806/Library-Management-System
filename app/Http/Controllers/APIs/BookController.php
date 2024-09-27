<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Books\StoreBookRequest;
use App\Http\Requests\Books\FilteringFormRequest;
use App\Http\Requests\Books\UpdateBookRequest;
use App\Models\Book;
use App\Services\BookService;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    use ResponseTrait;
    protected $bookService;

    /**
     * Create a new class instance.
     * @param \App\Services\BookService $bookService
     */
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Display a listing of the books.
     * @return \Illuminate\Http\Response
     */
    public function index(FilteringFormRequest $FilteringFormRequest)
    {
        $validated = $FilteringFormRequest->validated();
        $response = $this->bookService->index($validated);
        return $response['status']
            ? $this->getResponse("books", $response['books'], 200)
            : $this->getResponse("error", $response['msg'], 404);
    }

    /**
     * Display the specified book.
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        $response = $this->bookService->show($book);
        return $response['status']
            ? $this->getResponse("book", $response['book'], 200)
            : $this->getResponse("error", $response['msg'], 404);
    }

    /**
     * Store a newly created resource in storage.
     * @param \App\Http\Requests\Books\StoreBookRequest $storeBookRequest
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBookRequest $storeBookRequest)
    {
        $validatedData = $storeBookRequest->validated();
        $response = $this->bookService->store($validatedData);
        return $response['status']
            ? $this->getResponse("msg", "Created book successfully", 201)
            : $this->getResponse("msg", $response['msg'], $response['code']);
    }

    /**
     * Update the specified book in storage.
     * @param \App\Http\Requests\Books\UpdateBookRequest $updateBookRequest
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBookRequest $updateBookRequest, Book $book)
    {
        $validatedData = $updateBookRequest->validated();
        $response = $this->bookService->update($validatedData, $book);
        return $response['status']
            ? $this->getResponse("msg", "Updated book successfully", 200)
            : $this->getResponse("error", $response['msg'], 404);
    }

    /**
     * Remove the specified book from storage.
     * @param \App\Models\Book $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        if (!Auth::user()->is_admin) {
            return $this->getResponse('error', "Can't access to this permission", 400);
        }

        $book->delete();
        return $this->getResponse("msg", "Deleted book successfully", 200);
    }
}
