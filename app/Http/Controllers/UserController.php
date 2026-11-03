<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveUserRequest;
use App\Models\Department;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', User::class);
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);
        $search = $request->string('search')->trim()->toString();
        $role = $request->string('role')->toString();

        return Inertia::render('Users/Index', [
            'users' => User::query()->visibleTo($actor)->with(['institution:id,name', 'department:id,name'])
                ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                    ->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%')))
                ->when($role !== '', fn ($query) => $query->where('role', $role))
                ->orderBy('name')->paginate(10)->withQueryString()
                ->through(fn (User $user) => [
                    'id' => $user->id, 'name' => $user->name, 'email' => $user->email,
                    'role' => $user->role, 'institution_id' => $user->institution_id, 'department_id' => $user->department_id,
                    'institution' => $user->institution, 'department' => $user->department,
                    'can_update' => $actor->can('update', $user), 'can_delete' => $actor->can('delete', $user),
                ]),
            'institutions' => Institution::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'departments' => Department::query()->where('is_active', true)
                ->whereHas('institution', fn ($query) => $query->where('is_active', true))
                ->orderBy('name')->get(['id', 'name', 'institution_id']),
            'roles' => $actor->role === 'superadmin' ? ['superadmin', 'admin', 'operator'] : ['operator'],
            'filters' => ['search' => $search, 'role' => $role],
            'success' => $request->session()->get('success'),
        ]);
    }

    public function store(SaveUserRequest $request): RedirectResponse
    {
        $user = new User;
        $data = $request->validated();
        $user->forceFill([...$data, 'institution_id' => $data['institution_id'] ?? null, 'department_id' => $data['department_id'] ?? null])->save();

        return to_route('users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(SaveUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }
        if ($data['email'] !== $user->email) {
            $user->email_verified_at = null;
        }
        $user->forceFill([...$data, 'institution_id' => $data['institution_id'] ?? null, 'department_id' => $data['department_id'] ?? null])->save();

        return to_route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);
        if (DB::table('loans')->where('recorded_by', $user->id)->exists()
            || DB::table('loan_items')->where('returned_by', $user->id)->exists()
            || DB::table('item_transfers')->where('recorded_by', $user->id)->orWhere('approved_by', $user->id)->exists()) {
            throw ValidationException::withMessages(['user' => 'Pengguna tercatat dalam riwayat transaksi dan tidak dapat dihapus.']);
        }
        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
