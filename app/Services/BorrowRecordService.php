<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BorrowRecordService
{
    use ResponseTrait;

    /**
     * Display a listing of the borrow records.
     * @return array
     */
    public function index()
    {
        $borrows = BorrowRecord::all();
        if ($borrows->count() < 1) {
            return ['status'    =>  false,  'msg'   =>  'Not Found Any Borrow Records'];
        }
        $data = [];
        foreach ($borrows as $borrow) {
            $returnDate = Carbon::parse($borrow->returned_at);
            $dueDate = Carbon::parse($borrow->due_date);
            if (!$returnDate->isFuture()) {
                if ($dueDate === null) {
                    $borrow->returned_at = now();
                    $borrow->due_date = now();
                    $borrow->save();
                }
            }
            $data[] = [
                "book_title"            =>      $borrow->book->title,
                "user_name"             =>      $borrow->user->name,
                "borrowed_at"           =>      $borrow->borrowed_at,
                "due_date"              =>      $borrow->due_date,
                "returned_at"           =>      $borrow->returned_at
            ];
        }
        return ['status'    =>  true, 'records' =>  $data];
    }

    /**
     * Display the specified borrow record.
     * @param mixed $id
     * @return array
     */
    public function show($id)
    {
        $borrow = BorrowRecord::find($id);
        if (!$borrow) {
            return ['status'    =>  false,  'msg'   =>  "Not Found This Borrow Record"];
        }
        $data = [
            "book_title"            =>      $borrow->book->title,
            "user_name"             =>      $borrow->user->name,
            "borrowed_at"           =>      $borrow->borrowed_at,
            "due_date"              =>      $borrow->due_date,
            "returned_at"           =>      $borrow->returned_at
        ];
        return ['status'    =>  true, 'record'  =>  $data];
    }

    /**
     * Store a newly created borrow record in storage.
     * @param array $data
     * @throws \Exception
     * @return array
     */
    public function store(array $data)
    {
        $book = Book::find($data['book_id']);
        if (!$book) {
            return ['status' => false, 'msg' => 'Not Found This Book', 'code' => 404];
        }

        $borrowRecord = BorrowRecord::where("book_id", $data['book_id'])->first();

        if ($borrowRecord) {
            $date = Carbon::parse($borrowRecord->returned_at);
            if (!$date->isFuture()) {
                if ($borrowRecord->user_id === Auth::id()) {
                    $borrowRecord->due_date = null;
                    $borrowRecord->returned_at = now()->addDays(14);
                    $borrowRecord->save();
                    return ['status' => true];
                }
            } else {
                return ['status' => false, 'msg' => 'This book is already borrowed. Try again later', 'code' => 400];
            }
        }

        try {
            BorrowRecord::create([
                "book_id"     => $data['book_id'],
                "user_id"     => Auth::id(),
                "borrowed_at" => now(),
                "due_date"    => null,
                "returned_at" => now()->addDays(14)
            ]);
            return ['status' => true];
        } catch (Exception $e) {
            Log::error('Error creating borrow record: ' . $e->getMessage());
            return ['status' => false, 'msg' => 'There is an error on the server', 'code' => 500];
        }
    }



    /**
     * Update the specified borrow record in storage.
     * @param array $data
     * @param mixed $id
     * @return array
     */
    public function update(array $data, $id)
    {
        $borrow = BorrowRecord::find($id);
        if (!$borrow) {
            return ['status'    =>  false, 'msg'    =>  "Not Found This Borrow Record", 'code'  =>  404];
        }
        $book = Book::find($data['book_id']);
        if (!$book) {
            return ['status'    =>  false, 'msg'    =>  "Not Found This Book", 'code'  =>  404];
        }

        if ($borrow->user_id !== Auth::id()) {
            return ['status'    =>  false, 'msg'    =>  "unAuthenticated", 'code'  =>  401];
        }

        $date = Carbon::parse($data['borrowed_at']);
        if (!$date->isFuture()) {
            return ['status'    =>  false, 'msg'    =>  "The borrow date isn't future", 'code'  =>  400];
        }

        if ($data['returned_at'] < $data['borrowed_at']) {
            return ['status'    =>  false, 'msg'    =>  "The returned date must be after borrowed date", 'code'  =>  400];
        }

        try {
            $borrow->book_id = $data['book_id'];
            $borrow->borrowed_at = $data['borrowed_at'];
            $borrow->returned_at = $data['returned_at'];
            $borrow->save();
            return ['status'    =>  true];
        } catch (Exception $e) {
            Log::error('Error update borrow record: ' . $e->getMessage());
            return ['status' => false, 'msg' => 'There is an error on the server', 'code' => 500];
        }
    }

    /**
     * Remove the specified borrow record from storage.
     * @param mixed $id
     * @return array
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->as_admin == 'no') {
            return ['status' => false, 'msg' => 'Not have administration permissions', 'code' => 400];
        }
        $borrow = BorrowRecord::find($id);
        if (!$borrow) {
            return ['status'    =>  false, 'msg'    =>  "Not Found This Borrow Record", 'code'  =>  404];
        }
        if ($borrow->user_id !== Auth::id()) {
            return ['status'    =>  false, 'msg'    =>  "unAuthenticated", 'code'  =>  401];
        }
        $borrow->delete();
        return ['status'    =>  true];
    }

    /**
     * Return book
     * @param mixed $id
     * @return array
     */
    public function due($id)
    {
        $book = Book::find($id);
        if (!$book) {
            return ['status'    =>  false, 'msg'    =>  'Not Found This Book', 'code'   =>  404];
        }
        $record = BorrowRecord::where('book_id', $id)->where('user_id', Auth::id())->first();
        if (!$record) {
            return ['status'    =>  false, 'msg'    =>  'This Book Not For You', 'code'  =>  404];
        }

        if ($record->user_id !== Auth::id()) {
            return ['status'    =>  false, 'msg'    =>  "unAuthenticated", 'code'  =>  401];
        }

        $date = Carbon::parse($record->returned_at);
        if (!$date->isFuture()) {
            $record->due_date = null;
            $record->save();
            return ['status'    =>  false, 'msg'    =>  'Book Borrow Record is Expired already', 'code' =>  400];
        }

        if ($record->due_date !== null) {
            return ['status'    =>  false, 'msg'    =>  'Book is returned already', 'code' =>  400];
        }

        $record->due_date = now();
        $record->returned_at = now();
        $record->save();
        return ['status'    =>  true];
    }
}
