<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolIdCardSetting extends Model
{
    public const TEMPLATE_CLASSIC = 'classic';

    public const TEMPLATE_MODERN = 'modern';

    public const TEMPLATE_HORIZONTAL = 'horizontal';

    protected $fillable = [
        'school_id',
        'template',
        'primary_color',
        'secondary_color',
        'header_title',
        'footer_text',
        'show_fields',
    ];

    protected function casts(): array
    {
        return [
            'show_fields' => 'array',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function shows(string $field): bool
    {
        return (bool) ($this->show_fields[$field] ?? false);
    }

    public static function templateOptions(): array
    {
        return [
            self::TEMPLATE_CLASSIC => 'Classic — portrait, centered photo',
            self::TEMPLATE_MODERN => 'Modern — color sidebar with logo',
            self::TEMPLATE_HORIZONTAL => 'Horizontal — landscape layout',
        ];
    }
}
