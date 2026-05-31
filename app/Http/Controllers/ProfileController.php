<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function show(Request $request): Response
    {
        $user = $request->user()->only(['id', 'name', 'email', 'role', 'created_at', 'avatar']);
        $user['avatar_url'] = $request->user()->avatar
            ? Storage::disk('public')->url($request->user()->avatar)
            : null;

        return Inertia::render('Profile', ['user' => $user]);
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $ext      = $request->file('photo')->getClientOriginalExtension() ?: 'jpg';
        $filename = $user->id . '_' . time() . '.' . $ext;
        $dir      = storage_path('app/public/avatars');

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $request->file('photo')->move($dir, $filename);
        $path = 'avatars/' . $filename;

        $user->update(['avatar' => $path]);

        return back()->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function updateInfo(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($request->only('name', 'email'));

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.confirmed'                => 'Konfirmasi password tidak cocok.',
            'password.min'                      => 'Password minimal 8 karakter.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
