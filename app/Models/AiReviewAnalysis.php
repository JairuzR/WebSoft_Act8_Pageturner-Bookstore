<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiReviewAnalysis extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'book_id',
        'summary',
        'overall_sentiment',
        'sentiment_score',
        'sentiment_breakdown',
        'key_themes',
        'reviews_analyzed',
        'provider_used',
    ];

    protected $casts = [
        'sentiment_breakdown' => 'array',
        'key_themes'          => 'array',
        'sentiment_score'     => 'decimal:2',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function getSentimentColorAttribute(): string
    {
        return match ($this->overall_sentiment) {
            'positive' => 'green',
            'negative' => 'red',
            'neutral'  => 'gray',
            'mixed'    => 'yellow',
            default    => 'gray',
        };
    }

    public function getSentimentLabelAttribute(): string
    {
        return match ($this->overall_sentiment) {
            'positive' => 'Positive',
            'negative' => 'Negative',
            'neutral'  => 'Neutral',
            'mixed'    => 'Mixed',
            default    => 'Unknown',
        };
    }
}