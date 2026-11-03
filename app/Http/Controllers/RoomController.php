<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $institution = $request->string('institution_id')->toString();

        return Inertia::render('Rooms/Index', [
            'rooms' => Room::query()->with('institution:id,name')
                ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                    ->where('code', 'like', '%'.$search.'%')->orWhere('name', 'like', '%'.$search.'%')))
                ->when($institution !== '', fn ($query) => $query->where('institution_id', $institution))
                ->orderBy('name')->paginate(10)->withQueryString(),
            'institutions' => Institution::query()->orderBy('name')->get(['id', 'name']),
            'filters' => ['search' => $search, 'institution_id' => $institution],
            'can_create' => $request->user()?->can('create', Room::class),
            'success' => $request->session()->get('success'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $room = new Room;
        $room->forceFill($this->validated($request))->save();

        return to_route('rooms.index')->with('success', 'Ruang berhasil ditambahkan.');
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        Gate::authorize('update', $room);
        $room->forceFill($this->validated($request, $room))->save();

        return back()->with('success', 'Ruang berhasil diperbarui.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        Gate::authorize('delete', $room);
        DB::transaction(function () use ($room): void {
            $room = Room::query()->lockForUpdate()->whereKey($room->getKey())->firstOrFail();
            if ($room->units()->exists() || DB::table('item_transfers')->where('from_room_id', $room->getKey())->orWhere('to_room_id', $room->getKey())->exists()) {
                throw ValidationException::withMessages(['room' => 'Ruang masih digunakan unit atau riwayat mutasi. Nonaktifkan ruang sebagai gantinya.']);
            }
            $room->delete();
        });

        return back()->with('success', 'Ruang berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Room $room = null): array
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);

        return $request->validate([
            'institution_id' => [
                'required', 'integer', Rule::exists('institutions', 'id'),
                ...($actor->role === 'superadmin' ? [] : [Rule::in([$actor->institution_id])]),
                ...($room === null ? [] : [Rule::in([$room->institution_id])]),
            ],
            'code' => ['required', 'string', 'max:50', Rule::unique('rooms', 'code')
                ->where('institution_id', $request->input('institution_id'))->ignore($room)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
