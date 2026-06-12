<?php

namespace App\Services;

use App\Models\School;
use App\Models\SchoolIdCardSetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class IdCardService
{
    public function defaultShowFields(): array
    {
        return [
            'photo' => true,
            'roll_no' => true,
            'class' => true,
            'guardian' => true,
            'phone' => false,
            'barcode' => true,
        ];
    }

    public function validationRules(): array
    {
        return [
            'id_card_template' => ['required', Rule::in(array_keys(SchoolIdCardSetting::templateOptions()))],
            'id_card_primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'id_card_secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'id_card_header_title' => ['required', 'string', 'max:255'],
            'id_card_footer_text' => ['nullable', 'string', 'max:255'],
            'id_card_show_photo' => ['nullable', 'boolean'],
            'id_card_show_roll_no' => ['nullable', 'boolean'],
            'id_card_show_class' => ['nullable', 'boolean'],
            'id_card_show_guardian' => ['nullable', 'boolean'],
            'id_card_show_phone' => ['nullable', 'boolean'],
            'id_card_show_barcode' => ['nullable', 'boolean'],
            'school_logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function settingsFor(School $school): SchoolIdCardSetting
    {
        $settings = $school->idCardSettings;

        if ($settings) {
            return $settings;
        }

        return new SchoolIdCardSetting([
            'school_id' => $school->id,
            'template' => SchoolIdCardSetting::TEMPLATE_CLASSIC,
            'primary_color' => '#0a5f47',
            'secondary_color' => '#0d7a5c',
            'header_title' => 'Student Identity Card',
            'footer_text' => null,
            'show_fields' => $this->defaultShowFields(),
        ]);
    }

    public function saveForSchool(School $school, array $input): SchoolIdCardSetting
    {
        return SchoolIdCardSetting::updateOrCreate(
            ['school_id' => $school->id],
            [
                'template' => $input['id_card_template'],
                'primary_color' => $input['id_card_primary_color'],
                'secondary_color' => $input['id_card_secondary_color'] ?? null,
                'header_title' => $input['id_card_header_title'],
                'footer_text' => $input['id_card_footer_text'] ?? null,
                'show_fields' => $this->showFieldsFromInput($input),
            ]
        );
    }

    public function storeSchoolLogo(School $school, UploadedFile $file): string
    {
        $directory = "schools/{$school->id}";
        Storage::disk('public')->makeDirectory($directory);

        if ($school->logo) {
            Storage::disk('public')->delete($school->logo);
        }

        $path = $file->store($directory, 'public');
        $school->update(['logo' => $path]);

        return $path;
    }

    public function cardViewName(SchoolIdCardSetting $settings): string
    {
        $template = $settings->template;
        $allowed = array_keys(SchoolIdCardSetting::templateOptions());

        if (! in_array($template, $allowed, true)) {
            $template = SchoolIdCardSetting::TEMPLATE_CLASSIC;
        }

        return 'school.id-cards.'.$template;
    }

    protected function showFieldsFromInput(array $input): array
    {
        return [
            'photo' => filter_var($input['id_card_show_photo'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'roll_no' => filter_var($input['id_card_show_roll_no'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'class' => filter_var($input['id_card_show_class'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'guardian' => filter_var($input['id_card_show_guardian'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'phone' => filter_var($input['id_card_show_phone'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'barcode' => filter_var($input['id_card_show_barcode'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ];
    }
}
