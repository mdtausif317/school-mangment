<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PageMenu extends Model
{
    protected $table = 'pages_menu_list';

    protected $fillable = [
        'school_id',
        'parent_id',
        'title',
        'slug',
        'icon',
        'sort_order',
        'display',
    ];

    protected function casts(): array
    {
        return [
            'display' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(PageMenu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(PageMenu::class, 'parent_id')->orderBy('sort_order');
    }

    public function buttons(): HasMany
    {
        return $this->hasMany(PageButton::class, 'menu_id');
    }

    public function authEntries(): HasMany
    {
        return $this->hasMany(PageAuth::class, 'menu_id');
    }

    public function isVisible(): bool
    {
        return ! $this->display;
    }
}
