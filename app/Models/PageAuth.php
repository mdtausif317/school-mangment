<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageAuth extends Model
{
    protected $table = 'pages_auth';

    protected $fillable = [
        'school_id',
        'menu_id',
        'user_id',
        'designation_id',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(PageMenu::class, 'menu_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }
}
