<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function edit()
    {
        // Only regular users can access
        if (Auth::user()->role !== 'user') {
            abort(403);
        }

        $user = Auth::user();
        return view('user.profile.edit', compact('user'));
    }

    public function update(UpdateProfileRequest $request)
    {
        // Role check (user only) handled by UpdateProfileRequest::authorize()
        // Validation handled by UpdateProfileRequest::rules()

        $user      = Auth::user();
        $validated = $request->validated();

        $user->name    = $validated['name'];
        $user->email   = $validated['email'];
        $user->country = $validated['country'] ?? $user->country;
        $user->state   = $validated['state'] ?? $user->state;

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $user->profile_photo = $request->file('profile_photo')
                ->store('profiles/user', 'public');
        }

        $user->save();

        return redirect()->route('user.profile.edit')
            ->with('success', 'Profile updated successfully!');
    }

    public function editPassword()
    {
        if (Auth::user()->role !== 'user') {
            abort(403);
        }

        return view('user.profile.password');
    }

    public function updatePassword(Request $request)
    {
        if (Auth::user()->role !== 'user') {
            abort(403);
        }

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Current password is incorrect.'
            ]);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('user.profile.password')
            ->with('success', 'Password changed successfully!');
    }
}