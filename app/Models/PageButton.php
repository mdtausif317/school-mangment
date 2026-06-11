<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageButton extends Model
{
    protected $table = 'pages_buttons';

    protected $fillable = [
        'menu_id',
        'button_title',
        'button_link',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(PageMenu::class, 'menu_id');
    }

    public function authEntries(): HasMany
    {
        return $this->hasMany(PageButtonAuth::class, 'button_id');
    }

    public function isActive(): bool
    {
        return ! $this->status;
    }
}
