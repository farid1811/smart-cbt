<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUsernameRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function showAccountSettings()
    {
        $user = Auth::user();
        return view('admin.settings.account', compact('user'));
    }

    public function updateUsername(UpdateUsernameRequest $request)
    {
        $user = Auth::user();
        $user->username = $request->username;
        $user->save();

        return back()->with('success', 'Username berhasil diperbarui.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
