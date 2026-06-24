<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ManageUserController extends Controller
{
    public function index(Request $request)
    {
        $search  = $request->input('search');
        $perPage = $request->input('per_page', 10);

        $users = User::query()
            ->when($search, fn($q) => $q->where(fn($inner) =>
                $inner->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
            ))
            ->latest('last_active_at')
            ->paginate($perPage)
            ->withQueryString();

        $stats = [
            'total'    => User::count(),
            'active'   => User::where('status', 'Active')->count(),
            'admins'   => User::where('role', 'admin')->count(),
            'inactive' => User::where('status', 'Inactive')->count(),
        ];

        return view('admin.manage-user.index', compact('users', 'stats', 'search', 'perPage'));
    }

    // Route model binding pakai 'manageUser' agar tidak bentrok dengan {user} milik auth
    public function edit(User $manageUser)
    {
        return view('admin.manage-user.edit', ['user' => $manageUser]);
    }

    public function update(Request $request, User $manageUser)
    {
        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $manageUser->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('admin.manage-users.edit', $manageUser)
            ->with('success', 'Password akun berhasil diperbarui.');
    }

    public function verify(User $manageUser)
    {
        $manageUser->update([
            'status' => 'Active',
        ]);

        return redirect()->route('admin.manage-users.edit', $manageUser)
            ->with('success', 'Status akun berhasil diverifikasi menjadi lengkap.');
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
