<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('q')) {
            $term = trim($request->query('q'));
            $query->where(function ($sub) use ($term) {
                $sub->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        $users = $query->withCount(['enrollments', 'orders'])->latest()->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', Rule::in(['student', 'teacher', 'moderator', 'admin', 'super_admin'])],
            'status' => ['required', 'string', Rule::in(['active', 'suspended', 'inactive'])],
        ]);

        // Only super_admin or admin can create admins
        if (in_array($validated['role'], ['admin', 'super_admin']) && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Administrators can create administrative accounts.');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'status' => $validated['status'],
            'is_admin' => in_array($validated['role'], ['admin', 'super_admin']),
            'locale' => 'bn',
        ]);

        AdminAuditLog::create([
            'admin_user_id' => auth()->id(),
            'action' => 'user_created',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'reason' => "User '{$user->name}' ({$user->email}) created with role '{$user->role}'",
        ]);

        return redirect()->route('admin.users.index')->with('success', "User '{$user->name}' created successfully.");
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', 'string', Rule::in(['student', 'teacher', 'moderator', 'admin', 'super_admin'])],
            'status' => ['required', 'string', Rule::in(['active', 'suspended', 'inactive'])],
        ]);

        // Privilege escalation protection
        if ($validated['role'] !== $user->role && in_array($validated['role'], ['admin', 'super_admin']) && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Only Super Administrators can grant administrative privileges.');
        }

        $oldRole = $user->role;
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->role = $validated['role'];
        $user->status = $validated['status'];
        $user->is_admin = in_array($validated['role'], ['admin', 'super_admin']);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        AdminAuditLog::create([
            'admin_user_id' => auth()->id(),
            'action' => 'user_updated',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'reason' => "User '{$user->name}' updated (Role: {$oldRole} -> {$user->role}, Status: {$user->status})",
        ]);

        return redirect()->route('admin.users.index')->with('success', "User '{$user->name}' updated successfully.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Cannot delete Super Administrator.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
