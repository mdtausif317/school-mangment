<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolIdCardSetting extends Model
{
    public const TEMPLATE_CLASSIC = 'classic';

    public const TEMPLATE_MODERN = 'modern';

    public const TEMPLATE_HORIZONTAL = 'horizontal';

    public const TEMPLATE_CUSTOM = 'custom';

    protected $fillable = [
        'school_id',
        'template',
        'primary_color',
        'secondary_color',
        'header_title',
        'footer_text',
        'show_fields',
        'custom_html',
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

    public function isCustomTemplate(): bool
    {
        return $this->template === self::TEMPLATE_CUSTOM;
    }

    public static function templateOptions(): array
    {
        return [
            self::TEMPLATE_CLASSIC => 'Premium — centered portrait photo',
            self::TEMPLATE_MODERN => 'Modern — wave header, circle photo',
            self::TEMPLATE_HORIZONTAL => 'Professional — landscape layout',
            self::TEMPLATE_CUSTOM => 'Custom — your own HTML template',
        ];
    }
}
