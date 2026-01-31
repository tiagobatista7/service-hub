<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\DeleteProfileRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user()->load('profile');

        return inertia('Profile/Edit', [
            'user' => $user,
            'profile' => $user->profile,
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $user->update($request->only('name', 'email'));

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->only('phone', 'role')
        );

        return redirect()->route('profile.edit')->with('success', 'Profile updated.');
    }

    public function destroy(DeleteProfileRequest $request)
    {
        $user = $request->user();

        Auth::logout();

        $user->profile()->delete();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
