<?php

namespace App\Services;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolIdCardSetting;
use App\Models\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function placeholderHelp(): array
    {
        return [
            '{{school_name}}' => 'School name',
            '{{school_logo}}' => 'School logo image (empty if none)',
            '{{header_title}}' => 'Card title from settings',
            '{{footer_text}}' => 'Footer text',
            '{{primary_color}}' => 'Primary brand color',
            '{{secondary_color}}' => 'Secondary brand color',
            '{{student_name}}' => 'Student full name',
            '{{student_photo}}' => 'Student photo block',
            '{{roll_no}}' => 'Roll number',
            '{{class_name}}' => 'Class name',
            '{{guardian_name}}' => 'Guardian name',
            '{{phone}}' => 'Phone number',
            '{{barcode}}' => 'Barcode SVG block',
            '{{barcode_value}}' => 'Barcode text value',
            '{{#photo}}...{{/photo}}' => 'Show block only if photo is enabled',
            '{{#roll_no}}...{{/roll_no}}' => 'Show block only if roll no is enabled',
            '{{#class}}...{{/class}}' => 'Show block only if class is enabled',
            '{{#guardian}}...{{/guardian}}' => 'Show block only if guardian is enabled',
            '{{#phone}}...{{/phone}}' => 'Show block only if phone is enabled',
            '{{#barcode}}...{{/barcode}}' => 'Show block only if barcode is enabled',
            '{{#school_logo}}...{{/school_logo}}' => 'Show block only if school has a logo',
        ];
    }

    public function defaultCustomHtml(): string
    {
        return <<<'HTML'
<div class="id-card id-card-premium mx-auto" style="width:360px;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 12px 40px rgba(15,23,42,.14);position:relative;">
    <div style="position:absolute;top:0;left:0;width:90px;height:90px;background:linear-gradient(135deg,{{primary_color}} 50%,transparent 50%);"></div>
    <div style="position:absolute;bottom:0;right:0;width:90px;height:90px;background:linear-gradient(315deg,{{primary_color}} 55%,transparent 55%);"></div>
    <div style="position:relative;z-index:2;text-align:center;padding:1.25rem 1rem 0;">
        {{#school_logo}}{{school_logo}}{{/school_logo}}
        <div style="font-size:1rem;font-weight:800;color:#0f172a;">{{school_name}}</div>
        <div style="font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:{{primary_color}};margin-top:.2rem;">{{header_title}}</div>
    </div>
    {{#photo}}
    <div style="text-align:center;margin:1rem 0;">
        <div style="display:inline-block;padding:6px;border-radius:50%;background:linear-gradient(145deg,{{primary_color}},{{secondary_color}});">
            <div style="width:132px;height:132px;border-radius:50%;overflow:hidden;border:3px solid #fff;background:#f1f5f9;">{{student_photo}}</div>
        </div>
    </div>
    {{/photo}}
    <div style="text-align:center;font-size:1.35rem;font-weight:800;color:#0f172a;padding:0 1rem;">{{student_name}}</div>
    {{#class}}<div style="text-align:center;font-size:.82rem;font-weight:600;color:{{primary_color}};margin-top:.15rem;">{{class_name}}</div>{{/class}}
    <div style="max-width:280px;margin:1rem auto;padding:0 1rem;">
        {{#roll_no}}<div style="display:flex;justify-content:space-between;padding:.45rem 0;border-bottom:1px dashed #e2e8f0;font-size:.82rem;"><span style="font-size:.68rem;font-weight:600;color:#64748b;text-transform:uppercase;">Roll No</span><span style="font-weight:600;color:#1e293b;">{{roll_no}}</span></div>{{/roll_no}}
        {{#guardian}}<div style="display:flex;justify-content:space-between;padding:.45rem 0;border-bottom:1px dashed #e2e8f0;font-size:.82rem;"><span style="font-size:.68rem;font-weight:600;color:#64748b;text-transform:uppercase;">Guardian</span><span style="font-weight:600;color:#1e293b;">{{guardian_name}}</span></div>{{/guardian}}
        {{#phone}}<div style="display:flex;justify-content:space-between;padding:.45rem 0;font-size:.82rem;"><span style="font-size:.68rem;font-weight:600;color:#64748b;text-transform:uppercase;">Phone</span><span style="font-weight:600;color:#1e293b;">{{phone}}</span></div>{{/phone}}
    </div>
    {{#barcode}}<div style="padding:0 1rem .5rem;">{{barcode}}</div>{{/barcode}}
    <div style="background:linear-gradient(90deg,{{primary_color}},{{secondary_color}});color:#fff;font-size:.72rem;font-weight:600;text-align:center;padding:.55rem 1rem;">{{footer_text}}</div>
</div>
HTML;
    }

    public function previewValidationRules(): array
    {
        return array_merge($this->validationRules(), [
            'school_id' => ['nullable', 'integer', 'exists:schools,id'],
            'school_name' => ['nullable', 'string', 'max:255'],
        ]);
    }

    public function validationRules(): array
    {
        return [
            'id_card_template' => ['required', Rule::in(array_keys(SchoolIdCardSetting::templateOptions()))],
            'id_card_primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'id_card_secondary_color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'id_card_header_title' => ['required', 'string', 'max:255'],
            'id_card_footer_text' => ['nullable', 'string', 'max:255'],
            'id_card_custom_html' => ['nullable', 'required_if:id_card_template,custom', 'string', 'max:50000'],
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
            'custom_html' => $this->defaultCustomHtml(),
        ]);
    }

    public function settingsFromInput(array $input): SchoolIdCardSetting
    {
        $customHtml = $input['id_card_custom_html'] ?? null;

        if (($input['id_card_template'] ?? '') === SchoolIdCardSetting::TEMPLATE_CUSTOM && blank($customHtml)) {
            $customHtml = $this->defaultCustomHtml();
        }

        return new SchoolIdCardSetting([
            'template' => $input['id_card_template'],
            'primary_color' => $input['id_card_primary_color'],
            'secondary_color' => $input['id_card_secondary_color'] ?? null,
            'header_title' => $input['id_card_header_title'],
            'footer_text' => $input['id_card_footer_text'] ?? null,
            'show_fields' => $this->showFieldsFromInput($input),
            'custom_html' => $customHtml,
        ]);
    }

    public function dummyPreviewSchool(string $name, ?School $existing = null): School
    {
        $school = new School([
            'name' => $name,
            'logo' => $existing?->logo,
        ]);
        $school->id = $existing?->id ?? 1;

        if ($existing?->logoUrl()) {
            $school->previewLogoUrl = $existing->logoUrl();
        }

        return $school;
    }

    public function applyPreviewLogo(School $school, UploadedFile $file): void
    {
        $school->previewLogoUrl = 'data:'.$file->getMimeType().';base64,'.base64_encode(
            file_get_contents($file->getRealPath())
        );
    }

    public function dummyPreviewStudent(School $school): Student
    {
        $class = new SchoolClass([
            'name' => '10',
            'section' => 'A',
        ]);

        $student = new Student([
            'school_id' => $school->id,
            'name' => 'John Doe',
            'roll_no' => '101',
            'guardian_name' => 'Jane Doe',
            'phone' => '9876543210',
        ]);
        $student->id = 0;
        $student->isPreview = true;
        $student->setRelation('schoolClass', $class);
        $student->setRelation('school', $school);

        return $student;
    }

    public function buildCardRenderData(
        Student $student,
        School $school,
        SchoolIdCardSetting $settings
    ): array {
        $barcodeValue = $student->barcodeValue();
        $customHtml = null;

        if ($settings->isCustomTemplate()) {
            $customHtml = $this->renderCustomTemplate(
                $settings->custom_html ?? $this->defaultCustomHtml(),
                $student,
                $school,
                $settings,
                $barcodeValue
            );
        }

        return [
            'student' => $student,
            'school' => $school,
            'settings' => $settings,
            'barcodeValue' => $barcodeValue,
            'cardView' => $this->cardViewName($settings),
            'customHtml' => $customHtml,
        ];
    }

    public function saveForSchool(School $school, array $input): SchoolIdCardSetting
    {
        $customHtml = $input['id_card_custom_html'] ?? null;

        if (($input['id_card_template'] ?? '') === SchoolIdCardSetting::TEMPLATE_CUSTOM && blank($customHtml)) {
            $customHtml = $this->defaultCustomHtml();
        }

        return SchoolIdCardSetting::updateOrCreate(
            ['school_id' => $school->id],
            [
                'template' => $input['id_card_template'],
                'primary_color' => $input['id_card_primary_color'],
                'secondary_color' => $input['id_card_secondary_color'] ?? null,
                'header_title' => $input['id_card_header_title'],
                'footer_text' => $input['id_card_footer_text'] ?? null,
                'show_fields' => $this->showFieldsFromInput($input),
                'custom_html' => $customHtml,
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
        if ($settings->isCustomTemplate()) {
            return 'school.id-cards.custom';
        }

        $template = $settings->template;
        $allowed = [
            SchoolIdCardSetting::TEMPLATE_CLASSIC,
            SchoolIdCardSetting::TEMPLATE_MODERN,
            SchoolIdCardSetting::TEMPLATE_HORIZONTAL,
        ];

        if (! in_array($template, $allowed, true)) {
            $template = SchoolIdCardSetting::TEMPLATE_CLASSIC;
        }

        return 'school.id-cards.'.$template;
    }

    public function renderCustomTemplate(
        string $html,
        Student $student,
        School $school,
        SchoolIdCardSetting $settings,
        string $barcodeValue
    ): string {
        $html = $this->processConditionalBlocks($html, $settings, $school, $student);

        $className = $student->schoolClass?->displayName() ?? '—';

        $schoolLogo = $school->logoUrl()
            ? '<img src="'.e($school->logoUrl()).'" alt="" style="height:36px;margin-bottom:.35rem;">'
            : '';

        $studentPhoto = $student->photoUrl()
            ? '<img src="'.e($student->photoUrl()).'" alt="'.e($student->name).'" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">'
            : '<span style="color:#94a3b8;font-size:.75rem;">No Photo</span>';

        $barcode = '<div class="barcode-wrap mt-3 pt-3" style="border-top:1px dashed #dee2e6;">'
            .'<svg class="student-barcode"></svg>'
            .'<div class="barcode-text">'.e($barcodeValue).'</div>'
            .'</div>';

        $replacements = [
            '{{school_name}}' => e($school->name),
            '{{school_logo}}' => $schoolLogo,
            '{{header_title}}' => e($settings->header_title),
            '{{footer_text}}' => e($settings->footer_text ?? ''),
            '{{primary_color}}' => e($settings->primary_color),
            '{{secondary_color}}' => e($settings->secondary_color ?? $settings->primary_color),
            '{{student_name}}' => e($student->name),
            '{{student_photo}}' => $studentPhoto,
            '{{roll_no}}' => e($student->roll_no),
            '{{class_name}}' => e($className),
            '{{guardian_name}}' => e($student->guardian_name ?? ''),
            '{{phone}}' => e($student->phone ?? ''),
            '{{barcode}}' => $barcode,
            '{{barcode_value}}' => e($barcodeValue),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $html);
    }

    protected function processConditionalBlocks(
        string $html,
        SchoolIdCardSetting $settings,
        School $school,
        Student $student
    ): string {
        $conditionals = [
            'photo' => $settings->shows('photo'),
            'roll_no' => $settings->shows('roll_no'),
            'class' => $settings->shows('class'),
            'guardian' => $settings->shows('guardian') && filled($student->guardian_name),
            'phone' => $settings->shows('phone') && filled($student->phone),
            'barcode' => $settings->shows('barcode'),
            'school_logo' => (bool) $school->logoUrl(),
        ];

        foreach ($conditionals as $key => $show) {
            $pattern = '/\{\{#'.preg_quote($key, '/').'\}\}(.*?)\{\{\/'.preg_quote($key, '/').'\}\}/s';
            $html = preg_replace($pattern, $show ? '$1' : '', $html);
        }

        return $html;
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
