<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasRole('Superadmin')) abort(403);

        $users = User::with('roles')->where('id', '!=', Auth::id())->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasRole('Superadmin')) abort(403);
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->hasRole('Superadmin')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $newUser = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        $newUser->assignRole('Admin OPD');

        return redirect()->route('admin.users.index')->with('success', 'Admin OPD baru berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (!$authUser || !$authUser->hasRole('Superadmin')) abort(403);

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (!$authUser || !$authUser->hasRole('Superadmin')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Data admin & password berhasil diperbarui.');
    }

    public function toggleActive(User $user)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (!$authUser || !$authUser->hasRole('Superadmin')) abort(403);

        $user->update([
            'is_active' => !$user->is_active
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Status akun admin berhasil diubah.');
    }

    public function destroy(User $user)
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();
        if (!$authUser || !$authUser->hasRole('Superadmin')) abort(403);
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Akun admin berhasil dihapus.');
    }
}
