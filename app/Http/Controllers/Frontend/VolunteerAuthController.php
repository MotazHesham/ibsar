<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class VolunteerAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.volunteer-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $volunteer = Volunteer::where('email', $request->email)
            ->orWhere('identity_num', $request->email)
            ->first();

        if (!$volunteer || !$volunteer->password) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        if (!$volunteer->approved) {
            throw ValidationException::withMessages([
                'email' => [__('frontend.volunteer.not_approved')],
            ]);
        }

        if (!Hash::check($request->password, $volunteer->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        Auth::guard('volunteer')->login($volunteer, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('volunteer.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('volunteer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('volunteer.login');
    }
}
