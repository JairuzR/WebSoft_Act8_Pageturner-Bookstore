<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Laravel\Scout\Searchable;

class Book extends Model implements Auditable
{
    use HasFactory, AuditableTrait, Searchable;

    public function toSearchableArray(): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'author'      => $this->author,
            'publisher'   => $this->publisher,
            'description' => $this->description,
            'format'      => $this->format,
        ];
    }

    public function shouldBeSearchable(): bool
    {
        return $this->is_active;
    }

    protected $fillable = [
        'category_id',
        'title',
        'author',
        'isbn',
        'price',
        'stock_quantity',
        'description',
        'cover_image',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function aiAnalysis()
    {
        return $this->hasOne(AiReviewAnalysis::class)->latest();
    }
}