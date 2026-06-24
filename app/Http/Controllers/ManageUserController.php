<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ManageUserController extends Controller
{
    private const ROLE_OPTIONS = ['admin', 'user'];
    private const STATUS_OPTIONS = ['Active', 'Inactive'];

    public function index(Request $request)
    {
        $search  = $request->input('search');
        $role    = $request->input('role');
        $perPage = $request->input('per_page', 10);

        $users = User::query()
            ->when($search, fn($q) => $q->where(fn($inner) =>
                $inner->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
            ))
            ->when($role, fn($q) => $q->where('role', $role))
            ->latest('last_active_at')
            ->paginate($perPage)
            ->withQueryString();

        $stats = [
            'total'    => User::count(),
            'active'   => User::where('status', 'Active')->count(),
            'admins'   => User::where('role', 'admin')->count(),
            'inactive' => User::where('status', 'Inactive')->count(),
        ];

        return view('admin.manage-user.index', compact('users', 'stats', 'search', 'role', 'perPage'));
    }

    public function create()
    {
        return view('admin.manage-user.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:20'],
            'role'     => ['required', Rule::in(self::ROLE_OPTIONS)],
            'status'   => ['required', Rule::in(self::STATUS_OPTIONS)],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $data['password']       = Hash::make($data['password']);
        $data['terms_agreed']   = true;
        $data['last_active_at'] = now();

        User::create($data);

        return redirect()->route('admin.manage-users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    // Route model binding pakai 'manageUser' agar tidak bentrok dengan {user} milik auth
    public function edit(User $manageUser)
    {
        return view('admin.manage-user.edit', ['user' => $manageUser]);
    }

    public function update(Request $request, User $manageUser)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users')->ignore($manageUser->id)],
            'phone_number' => ['required', 'string', 'max:20'],
            'role'     => ['required', Rule::in(self::ROLE_OPTIONS)],
            'status'   => ['required', Rule::in(self::STATUS_OPTIONS)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $manageUser->update($data);

        return redirect()->route('admin.manage-users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $manageUser)
    {
        if ($manageUser->is(auth()->user())) {
            return redirect()->route('admin.manage-users.index')
                ->with('error', 'Anda tidak bisa menghapus akun yang sedang digunakan.');
        }

        if ($manageUser->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('admin.manage-users.index')
                ->with('error', 'Minimal harus ada satu akun admin aktif.');
        }

        $manageUser->delete();

        return redirect()->route('admin.manage-users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
