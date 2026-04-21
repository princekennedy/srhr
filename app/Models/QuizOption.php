<?php

namespace App\Models;

use App\Models\Concerns\BelongsToWebsite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizOption extends Model
{
    use BelongsToWebsite;
    use HasFactory;

    protected $fillable = [
        'website_id',
        'quiz_question_id',
        'option_text',
        'feedback',
        'is_correct',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_correct' => 'boolean',
        ];
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class, 'quiz_question_id');
    }
}