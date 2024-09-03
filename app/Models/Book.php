<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        "title",
        "author",
        "description",
        "published_at"
    ];

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
}
