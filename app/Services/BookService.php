<?php

namespace App\Services;

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
        $query = Book::query();

        if (isset($data['author']) && !empty($data['author'])) {
            $query->where('author', 'like', '%' . $data['author'] . '%');
        }

        if (isset($data['available_books']) && !empty($data['available_books'])) {
            $query->whereDoesntHave('borrowRecord', function ($q) {
                $q->where('returned_at', '>', now());
            });
        }

        $books = $query->with(['rating.user'])->get();

        if ($books->isEmpty()) {
            return ['status' => false, 'msg' => 'No books found.'];
        }

        $responseData = [];
        foreach ($books as $book) {
            $ratings = [];
            $totalRating = 0;
            $ratingCount = 0;

            if ($book->rating->isNotEmpty()) {
                foreach ($book->rating as $rating) {
                    $ratings[] = [
                        "user name" => $rating->user->name,
                        "rating"    => $rating->rating
                    ];
                    $totalRating += $rating->rating;
                    $ratingCount++;
                }
            }
            $averageRating = $ratingCount > 0 ? $totalRating / $ratingCount : null;
            $responseData[] = [
                'title'       => $book->title,
                'author'      => $book->author,
                'description' => $book->description,
                'published_at' => $book->published_at,
                'ratings'     => $ratings,
                'average_rating' => $averageRating,
            ];
        }

        return ['status' => true, 'books' => $responseData];
    }


    /**
     * Display the specified book.
     * @param mixed $id
     * @return array
     */
    public function show($id)
    {
        $book = Book::find($id);
        if (!$book) {
            return ['status'    =>  false, 'msg'    =>  "Not Found This Book"];
        }
        $ratings = [];
        $ratings_count = 0;
        $total_ratings = 0;
        if ($book->rating()->count() > 0) {
            foreach ($book->rating as $rating) {
                $ratings[] = [
                    "user_name"     =>      $rating->user->name,
                    "rating"        =>      $rating->rating
                ];
                $total_ratings += $rating->rating;
                $ratings_count++;
            }
        }
        $averageRating = $ratings_count > 0 ? $total_ratings / $ratings_count : null;
        $data = [
            "title"             =>      $book->title,
            "author"            =>      $book->author,
            "description"       =>      $book->description,
            "published_at"      =>      $book->published_at,
            "ratings"           =>      $ratings,
            "ratings_avg"       =>      $averageRating
        ];
        return ['status'    =>  true, 'book'    =>  $data];
    }

    /**
     * Store a newly created resource in storage.
     * @param array $data
     * @throws \Exception
     * @return boolean[]
     */
    public function store(array $data)
    {
        $user = Auth::user();
        if ($user->is_admin == false) {
            return ['status' => false, 'msg' => 'Not have administration permissions', 'code' => 400];
        }
        $date = Carbon::now();
        try {
            Book::create([
                "title"         =>      $data['title'],
                "author"        =>      $data['author'],
                "description"   =>      $data['description'],
                "published_at"  =>      $date
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
     * @param mixed $id
     * @return array
     */
    public function update(array $data, $id)
    {
        $user = Auth::user();
        if ($user->is_admin == false) {
            return ['status' => false, 'msg' => 'Not have administration permissions', 'code' => 400];
        }
        $book = Book::find($id);
        if (!$book) {
            return ['status'    =>  false, 'msg'    =>  "Not Found This Book", 'code'   =>  404];
        }
        try {
            $book->title = $data['title'];
            $book->author = $data['author'];
            $book->description = $data['description'];
            $book->save();
            return ['status'    =>  true];
        } catch (Exception $e) {
            Log::error('Error update book: ' . $e->getMessage());
            return ['status' => false, 'msg' => 'There is an error on the server', 'code' => 500];
        }
    }

    /**
     * Remove the specified book from storage.
     * @param mixed $id
     * @return array
     */
    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->is_admin == false) {
            return ['status' => false, 'msg' => 'Not have administration permissions', 'code' => 400];
        }
        $book = Book::find($id);
        if (!$book) {
            return ['status'    =>  false, 'msg'    =>  "Not Found This Book", "code"   =>  404];
        }
        $book->delete();
        return ['status'    =>  true];
    }
}
