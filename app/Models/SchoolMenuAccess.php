<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolMenuAccess extends Model
{
    protected $table = 'school_menu_access';

    protected $fillable = [
        'school_id',
        'menu_id',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(PageMenu::class, 'menu_id');
    }
}
