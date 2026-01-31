<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return inertia('Profile/Show', [
            'user' => $user,
        ]);
    }

    public function edit()
    {
        $user = Auth::user()->load('profile');

        return inertia('Profile/Edit', [
            'user' => $user,
            'profile' => $user->profile,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        if ($validatedData['email'] !== $user->email) {
            $user->email_verified_at = null;
        }

        $user->fill($validatedData);
        $user->save();

        return redirect('/profile')->with('status', 'profile-updated');
    }

    public function destroy(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'password' => ['required'],
        ]);

        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Senha incorreta']);
        }

        Auth::logout();

        $user->delete();

        return redirect('/')->with('success', 'Conta deletada com sucesso!');
    }
}
