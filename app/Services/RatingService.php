<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Rating;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RatingService
{
    use ResponseTrait;

    /**
     * Display the specified rating.
     * @param mixed $id
     * @return array
     */
    public function show($id)
    {
        $rating = Rating::find($id);
        if (!$rating) {
            return ['status'    =>  false, 'msg'    =>  "Not Found This rating"];
        }
        $data = [
            "user name"             =>      $rating->user->name,
            "book title"            =>      $rating->book->title,
            "rating"                =>      $rating->rating,
            "review"                =>      $rating->review
        ];
        return ['status'    =>  true, 'rating'    =>  $data];
    }

    /**
     * Store a newly created rating in storage.
     * @param array $data
     * @return array
     */
    public function store(array $data)
    {
        $book = Book::find($data['book_id']);
        if (!$book) {
            return ['status' => false, 'msg' => 'Not Found This Book', 'code' => 404];
        }

        $borrow = BorrowRecord::where('book_id', $data['book_id'])->where('user_id', Auth::id())->first();
        if (!$borrow) {
            return ['status' => false, 'msg' => 'You don\'t borrowed this book. Try after borrow', 'code' => 400];
        }

        $rating = Rating::where('book_id', $data['book_id'])->where('user_id', Auth::id())->first();
        if ($rating) {
            return ['status' => false, 'msg' => 'You have rating to this book.', 'code' => 400];
        }

        try {
            Rating::create([
                "user_id"       =>      Auth::id(),
                "book_id"       =>      $data['book_id'],
                "rating"        =>      $data['rating'],
                "review"        =>      $data['review'] ?? null
            ]);
            return ['status'    =>  true];
        } catch (Exception $e) {
            Log::error('Error creating rating: ' . $e->getMessage());
            return ['status' => false, 'msg' => 'There is an error on the server', 'code' => 500];
        }
    }

    /**
     * Update the specified rating in storage.
     * @param array $data
     * @param mixed $id
     * @return array
     */
    public function update(array $data, $id)
    {
        $rating = Rating::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$rating) {
            return ['status'    =>  false, 'msg'    =>  "Not Found This Rating", 'code' =>  404];
        }
        try {
            $filteredData = array_filter($data, function ($value) {
                return !is_null($value) && trim($value) !== '';
            });

            $rating->update($filteredData);
            return ['status'    =>  true];
        } catch (Exception $e) {
            Log::error('Error update rating: ' . $e->getMessage());
            return ['status' => false, 'msg' => 'There is an error on the server', 'code' => 500];
        }
    }

    /**
     * Remove the specified rating from storage.
     * @param mixed $id
     * @return array
     */
    public function destroy($id)
    {
        $rating = Rating::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$rating) {
            return ['status' => false, 'msg' => 'Not Found This rating', 'code' => 404];
        }
        $rating->delete();
        return ['status'    =>  true];
    }
}
