<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ratings = [];      // Array to store rating related to specified book
        $ratings_count = 0; // Calculate ratings number related to specified book to make average
        $total_ratings = 0; // Sum all ratings values to same book to make average

        foreach ($this->rating as $rating) {
            $ratings[] = [
                "user_name"     =>      $rating->user->name,
                "rating"        =>      $rating->rating
            ];
            $total_ratings += $rating->rating;
            $ratings_count++;
        }
        $averageRating = $ratings_count > 0 ? $total_ratings / $ratings_count : 0;

        return [
            "title"             =>   $this->title,
            "author"            =>   $this->author,
            "description"       =>   $this->description,
            "published_at"      =>   $this->published_at,
            "category"          =>   $this->category->name,
            "ratings"           =>   $ratings,
            "ratings_avg"       =>   $averageRating
        ];
    }
}
