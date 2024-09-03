<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Requests\Books\BookFormRequest;
use App\Http\Requests\Books\FilteringFormRequest;
use App\Services\BookService;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Log;

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
            : $this->getResponse("msg", $response['msg'], 404);
    }

    /**
     * Display the specified book.
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response = $this->bookService->show($id);
        return $response['status']
            ? $this->getResponse("book", $response['book'], 200)
            : $this->getResponse("msg", $response['msg'], 404);
    }

    /**
     * Store a newly created resource in storage.
     * @param \App\Http\Requests\Books\BookFormRequest $createBookFormRequest
     * @return \Illuminate\Http\Response
     */
    public function store(BookFormRequest $createBookFormRequest)
    {
        $validatedData = $createBookFormRequest->validated();
        $response = $this->bookService->store($validatedData);
        return $response['status']
            ? $this->getResponse("msg", "Created book successfully", 201)
            : $this->getResponse("msg", $response['msg'], $response['code']);
    }

    /**
     * Update the specified book in storage.
     * @param \App\Http\Requests\Books\BookFormRequest $BookFormRequest
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function update(BookFormRequest $BookFormRequest, $id)
    {
        $validatedData = $BookFormRequest->validated();
        $response = $this->bookService->update($validatedData, $id);
        return $response['status']
            ? $this->getResponse("msg", "Updated book successfully", 200)
            : $this->getResponse("msg", $response['msg'], 404);
    }

    /**
     * Remove the specified book from storage.
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = $this->bookService->destroy($id);
        return $response['status']
            ? $this->getResponse("msg", "Deleted book successfully", 200)
            : $this->getResponse("msg", $response['msg'], $response['code']);
    }
}
