<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\AccessMenuService;
use App\Services\IdCardService;
use App\Services\SchoolService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function __construct(
        protected SchoolService $schoolService,
        protected SubscriptionService $subscriptions,
        protected AccessMenuService $accessMenu,
        protected IdCardService $idCards
    ) {}

    public function create(): View
    {
        return view('super-admin.create-school', [
            'plans' => $this->subscriptions->getActivePlans(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(array_merge([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:schools,slug'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
            'portal_enabled' => ['nullable', 'boolean'],
            'subscription_plan_id' => ['nullable', 'exists:subscription_plans,id'],
        ], $this->idCards->validationRules()));

        $school = $this->schoolService->createSchool(
            collect($validated)->only(['name', 'slug', 'email', 'phone', 'address'])->all(),
            [
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => $validated['admin_password'],
            ],
            $request->boolean('portal_enabled'),
            $validated['subscription_plan_id'] ?? null,
            $request->only([
                'id_card_template',
                'id_card_primary_color',
                'id_card_secondary_color',
                'id_card_header_title',
                'id_card_footer_text',
                'id_card_custom_html',
                'id_card_show_photo',
                'id_card_show_roll_no',
                'id_card_show_class',
                'id_card_show_guardian',
                'id_card_show_phone',
                'id_card_show_barcode',
            ]),
            $request->file('school_logo')
        );

        return redirect()
            ->route('super-admin.dashboard')
            ->with('success', "School \"{$school->name}\" created successfully.");
    }

    public function access(School $school): View
    {
        return view('super-admin.school-access', [
            'school' => $school,
            'menus' => $this->accessMenu->getSchoolMenuTree(),
            'selectedMenuIds' => $this->accessMenu->getSchoolEnabledMenuIds($school->id),
            'plans' => $this->subscriptions->getActivePlans(),
            'subscriptionStatus' => $this->subscriptions->subscriptionStatusLabel($school),
            'activeSubscription' => $this->subscriptions->getActiveSubscription($school),
            'idCardSettings' => $this->idCards->settingsFor($school),
        ]);
    }

    public function previewIdCard(Request $request): View
    {
        $request->validate($this->idCards->previewValidationRules());

        $existingSchool = $request->filled('school_id')
            ? School::query()->find($request->integer('school_id'))
            : null;

        $schoolName = $request->input('school_name')
            ?? $existingSchool?->name
            ?? 'Sample School';

        $previewSchool = $this->idCards->dummyPreviewSchool($schoolName, $existingSchool);

        if ($request->hasFile('school_logo')) {
            $this->idCards->applyPreviewLogo($previewSchool, $request->file('school_logo'));
        }

        $settings = $this->idCards->settingsFromInput($request->all());
        $student = $this->idCards->dummyPreviewStudent($previewSchool);

        return view('school.id-cards.print', array_merge(
            $this->idCards->buildCardRenderData($student, $previewSchool, $settings),
            ['isPreview' => true]
        ));
    }

    public function updateIdCard(Request $request, School $school): RedirectResponse
    {
        $validated = $request->validate($this->idCards->validationRules());

        $this->idCards->saveForSchool($school, $request->only([
            'id_card_template',
            'id_card_primary_color',
            'id_card_secondary_color',
            'id_card_header_title',
            'id_card_footer_text',
            'id_card_custom_html',
            'id_card_show_photo',
            'id_card_show_roll_no',
            'id_card_show_class',
            'id_card_show_guardian',
            'id_card_show_phone',
            'id_card_show_barcode',
        ]));

        if ($request->hasFile('school_logo')) {
            $this->idCards->storeSchoolLogo($school, $request->file('school_logo'));
        }

        return back()->with('success', 'ID card design updated.');
    }

    public function updateAccess(Request $request, School $school): RedirectResponse
    {
        $validated = $request->validate([
            'menu_ids' => ['nullable', 'array'],
            'menu_ids.*' => ['integer', 'exists:pages_menu_list,id'],
        ]);

        $this->schoolService->syncSchoolMenuAccess($school, $validated['menu_ids'] ?? []);

        return redirect()
            ->route('super-admin.schools.access', $school)
            ->with('success', "Menu access updated for \"{$school->name}\".");
    }
}
