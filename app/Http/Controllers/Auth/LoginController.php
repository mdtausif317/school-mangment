<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        protected SubscriptionService $subscriptions
    ) {}

    public function showSuperAdminLogin(): View
    {
        return view('auth.super-admin-login');
    }

    public function superAdminLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            if (! $user->isSuperAdmin()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Not a super admin account.'])->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->route('super-admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->isSuperAdmin()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Use the super admin login page.'])->onlyInput('email');
            }

            if ($user->isStudent()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Students must use the student login page.'])->onlyInput('email');
            }

            if (! $user->is_active || ! $user->school?->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Account or school is inactive.'])->onlyInput('email');
            }

            $request->session()->regenerate();

            if (! $this->subscriptions->hasActiveSubscription($user->school)) {
                return redirect()->route('school.subscription.expired');
            }

            return redirect()->route('school.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function showStudentLogin(): View
    {
        return view('auth.student-login');
    }

    public function studentLogin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (! $user->isStudent()) {
                Auth::logout();
                return back()->withErrors(['email' => 'Not a student account. Use school or staff login.'])->onlyInput('email');
            }

            if (! $user->is_active || ! $user->school?->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Account or school is inactive.'])->onlyInput('email');
            }

            if (! $user->linkedStudentProfile()) {
                Auth::logout();
                return back()->withErrors(['email' => 'No student record in your school for this email. Contact your school admin.'])->onlyInput('email');
            }

            $request->session()->regenerate();

            if (! $this->subscriptions->hasActiveSubscription($user->school)) {
                return redirect()->route('school.subscription.expired');
            }

            return redirect()->route('student.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        $wasSuperAdmin = $request->user()?->isSuperAdmin();
        $wasStudent = $request->user()?->isStudent();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect($wasSuperAdmin
            ? route('super-admin.login')
            : ($wasStudent ? route('student.login') : route('login')));
    }
}
