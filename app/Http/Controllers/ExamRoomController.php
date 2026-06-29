<?php

namespace App\Http\Controllers;

use App\Models\ExamRoom;
use Illuminate\Http\Request;

class ExamRoomController extends Controller
{
    public function index()
    {
        return view('admin.exam-rooms.index', [
            'rooms' => ExamRoom::withCount('exams')->orderBy('name')->paginate(10),
        ]);
    }

    public function create()
    {
        return view('admin.exam-rooms.form', [
            'room' => new ExamRoom(),
        ]);
    }

    public function store(Request $request)
    {
        ExamRoom::create($this->validatedData($request));

        return redirect()->route('admin.exam-rooms.index')
            ->with('success', 'Ruangan ujian berhasil ditambahkan.');
    }

    public function edit(ExamRoom $examRoom)
    {
        return view('admin.exam-rooms.form', [
            'room' => $examRoom,
        ]);
    }

    public function update(Request $request, ExamRoom $examRoom)
    {
        $examRoom->update($this->validatedData($request, $examRoom));

        return redirect()->route('admin.exam-rooms.index')
            ->with('success', 'Ruangan ujian berhasil diperbarui.');
    }

    public function destroy(ExamRoom $examRoom)
    {
        if ($examRoom->exams()->exists()) {
            return redirect()->route('admin.exam-rooms.index')
                ->with('error', 'Ruangan tidak bisa dihapus karena sudah dipakai pada data ujian.');
        }

        $examRoom->delete();

        return redirect()->route('admin.exam-rooms.index')
            ->with('success', 'Ruangan ujian berhasil dihapus.');
    }

    private function validatedData(Request $request, ?ExamRoom $room = null): array
    {
        $roomId = $room?->id ?? 'NULL';

        return $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:exam_rooms,name,' . $roomId],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000'],
        ], [
            'name.required' => 'Nama ruangan wajib diisi.',
            'name.unique' => 'Nama ruangan sudah digunakan.',
            'capacity.required' => 'Kapasitas wajib diisi.',
            'capacity.integer' => 'Kapasitas harus berupa angka.',
            'capacity.min' => 'Kapasitas minimal 1 kursi.',
        ]);
    }
}
