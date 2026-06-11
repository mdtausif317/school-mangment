<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageButtonAuth extends Model
{
    protected $table = 'pages_buttons_auth';

    protected $fillable = [
        'button_id',
        'user_id',
        'designation_id',
    ];

    public function button(): BelongsTo
    {
        return $this->belongsTo(PageButton::class, 'button_id');
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
