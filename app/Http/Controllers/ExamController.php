<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExamController extends Controller
{
    public function index()
    {
        return view('admin.exams.index', [
            'exams' => Exam::with(['rooms', 'sessions'])->latest('start_date')->paginate(10),
        ]);
    }

    public function create()
    {
        return view('admin.exams.form', [
            'exam' => new Exam(),
            'rooms' => ExamRoom::orderBy('name')->get(),
            'sessionRows' => $this->defaultSessionRows(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data) {
            $exam = Exam::create($data['exam']);
            $exam->rooms()->sync($data['room_ids']);
            $this->syncSessions($exam, $data['sessions']);
        });

        return redirect()->route('admin.exams.index')
            ->with('success', 'Data ujian berhasil ditambahkan.');
    }

    public function edit(Exam $exam)
    {
        $exam->load(['rooms', 'sessions']);

        return view('admin.exams.form', [
            'exam' => $exam,
            'rooms' => ExamRoom::orderBy('name')->get(),
            'sessionRows' => $this->sessionRows($exam),
        ]);
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($exam, $data) {
            $exam->update($data['exam']);
            $exam->rooms()->sync($data['room_ids']);
            $this->syncSessions($exam, $data['sessions']);
        });

        return redirect()->route('admin.exams.index')
            ->with('success', 'Data ujian berhasil diperbarui.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('admin.exams.index')
            ->with('success', 'Data ujian berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'exam_room_ids' => ['required', 'array', 'min:1'],
            'exam_room_ids.*' => ['integer', 'exists:exam_rooms,id'],
            'session_count' => ['required', 'integer', 'min:1', 'max:3'],
            'sessions' => ['required', 'array', 'min:1'],
            'sessions.*.start_time' => ['nullable', 'date_format:H:i'],
            'sessions.*.end_time' => ['nullable', 'date_format:H:i'],
        ], [
            'name.required' => 'Nama ujian wajib diisi.',
            'start_date.required' => 'Tanggal mulai ujian wajib diisi.',
            'end_date.required' => 'Tanggal selesai ujian wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'exam_room_ids.required' => 'Minimal satu ruangan ujian wajib dipilih.',
            'exam_room_ids.array' => 'Pilihan ruangan tidak valid.',
            'exam_room_ids.min' => 'Minimal satu ruangan ujian wajib dipilih.',
            'exam_room_ids.*.exists' => 'Salah satu ruangan ujian tidak valid.',
            'session_count.required' => 'Jumlah sesi wajib diisi.',
            'session_count.integer' => 'Jumlah sesi harus berupa angka.',
            'session_count.min' => 'Jumlah sesi minimal 1.',
            'session_count.max' => 'Jumlah sesi maksimal 3.',
            'sessions.required' => 'Minimal satu sesi wajib diisi.',
            'sessions.*.start_time.date_format' => 'Jam mulai sesi harus memakai format HH:MM.',
            'sessions.*.end_time.date_format' => 'Jam selesai sesi harus memakai format HH:MM.',
        ]);

        $sessionCount = (int) $validated['session_count'];
        $sessions = collect($validated['sessions'])
            ->take($sessionCount)
            ->map(fn (array $session) => [
                'start_time' => $session['start_time'] ?? null,
                'end_time' => $session['end_time'] ?? null,
            ])
            ->values();

        if ($sessions->count() !== $sessionCount) {
            throw ValidationException::withMessages([
                'session_count' => 'Jumlah baris sesi tidak sesuai.',
            ]);
        }

        $sessions->each(function (array $session, int $index) {
            if (! $session['start_time'] || ! $session['end_time']) {
                throw ValidationException::withMessages([
                    'sessions.' . $index . '.start_time' => 'Jam mulai dan selesai sesi harus lengkap.',
                ]);
            }

            if ($session['end_time'] <= $session['start_time']) {
                throw ValidationException::withMessages([
                    'sessions.' . $index . '.end_time' => 'Jam selesai harus setelah jam mulai.',
                ]);
            }
        });

        return [
            'exam' => [
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'exam_room_id' => $validated['exam_room_ids'][0],
            ],
            'room_ids' => $validated['exam_room_ids'],
            'sessions' => $sessions->all(),
        ];
    }

    private function syncSessions(Exam $exam, array $sessions): void
    {
        $exam->sessions()->delete();

        foreach ($sessions as $index => $session) {
            $exam->sessions()->create([
                'order' => $index + 1,
                'start_time' => $session['start_time'],
                'end_time' => $session['end_time'],
            ]);
        }
    }

    private function defaultSessionRows(): array
    {
        return [
            ['start_time' => '', 'end_time' => ''],
            ['start_time' => '', 'end_time' => ''],
            ['start_time' => '', 'end_time' => ''],
        ];
    }

    private function sessionRows(Exam $exam): array
    {
        $rows = $exam->sessions
            ->map(fn ($session) => [
                'start_time' => $session->start_time?->format('H:i') ?? '',
                'end_time' => $session->end_time?->format('H:i') ?? '',
            ])
            ->values()
            ->all();

        return array_pad($rows, 3, ['start_time' => '', 'end_time' => '']);
    }
}
