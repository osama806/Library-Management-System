<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "author",
        "description",
        "published_at",
        "category_id"
    ];

    /**
     * Get category related to this model.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the borrowRecord related to this model.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function borrowRecord(): HasMany
    {
        return $this->hasMany(BorrowRecord::class);
    }

    /**
     * Get the ratings related to this model.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rating(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Filter books by author
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $author
     * @return Builder
     */
    public function scopeAuthor(Builder $query, $author = null)
    {
        if ($author) {
            $query->where('author',  $author);
        }

        return $query;
    }

    /**
     * Filter books by books available (Not Borrowed)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $available
     * @return Builder
     */
    public function scopeAvailableBooks(Builder $query, $available = null)
    {
        if ($available) {
            // From borrowRecord relation
            $query->whereHas('borrowRecord', function ($q) {
                // Get all books that not borrowed
                $q->where('returned_at', '>', now());
            });
        }

        return $query;
    }

    /**
     * Filter books by category related it
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $categoryName
     * @return Builder
     */
    public function scopeCategory(Builder $query, $categoryName = null)
    {
        if ($categoryName) {
            // using function ($q) use ($categoryName) because category relation is belongsTo
            $query->whereHas('category', function ($q) use ($categoryName) {
                $q->where('name', $categoryName);
            });
        }

        return $query;
    }
}
