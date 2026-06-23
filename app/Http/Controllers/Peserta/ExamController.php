<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\Result;
use App\Models\TryoutPackage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    /**
     * Mulai sesi ujian baru.
     */
    public function start(TryoutPackage $package)
    {
        // Cek apakah tryout tersedia
        if (!$package->isAvailable()) {
            return redirect()->route('peserta.dashboard')->with('error', 'Tryout ini tidak tersedia saat ini.');
        }

        // Cek apakah sudah ada sesi aktif untuk tryout ini
        $existingSession = ExamSession::where('user_id', Auth::id())
            ->where('tryout_package_id', $package->id)
            ->where('status', 'berlangsung')
            ->first();

        if ($existingSession) {
            // Cek apakah sesi sudah expired
            if ($existingSession->isExpired()) {
                $this->processSubmit($existingSession, 'timeout');
                return redirect()->route('peserta.exam.result', $existingSession->result)->with('info', 'Waktu ujian Anda telah habis dan otomatis dikumpulkan.');
            }
            return redirect()->route('peserta.exam.show', [$existingSession->id, 1]);
        }

        // Cek attempt limit
        $userId = Auth::id();
        $attemptsCount = \App\Models\PackageAttempt::where('participant_id', $userId)
            ->where('package_id', $package->id)
            ->count();

        if ($attemptsCount >= $package->attempt_limit) {
            return redirect()->route('peserta.dashboard')->with('error', 'Anda telah mencapai batas maksimum percobaan (' . $package->attempt_limit . 'x) untuk paket ini.');
        }

        // Buat record attempt baru
        \App\Models\PackageAttempt::create([
            'participant_id' => $userId,
            'package_id'     => $package->id,
            'attempt_number' => $attemptsCount + 1,
            'started_at'     => Carbon::now(),
        ]);

        // Buat sesi ujian baru
        $session = ExamSession::create([
            'user_id'           => $userId,
            'tryout_package_id' => $package->id,
            'started_at'        => Carbon::now(),
            'status'            => 'berlangsung',
        ]);

        $questions = $package->questions;
        $questionIds = $questions->pluck('id')->toArray();
        shuffle($questionIds); // Acak urutan soal

        // Simpan urutan acak ke session
        $session->update(['soal_order' => $questionIds]);

        // Pre-create jawaban kosong untuk semua soal dengan opsi teracak
        foreach ($questions as $question) {
            $opts = ['A', 'B', 'C', 'D'];
            if ($question->opsi_e) {
                $opts[] = 'E';
            }
            $shuffledOpts = $opts;
            shuffle($shuffledOpts);

            $mapping = [];
            foreach ($opts as $index => $visualKey) {
                $mapping[$visualKey] = $shuffledOpts[$index];
            }

            ExamAnswer::create([
                'exam_session_id' => $session->id,
                'question_id'     => $question->id,
                'jawaban'         => null,
                'is_ragu'         => false,
                'options_mapping' => $mapping,
            ]);
        }

        return redirect()->route('peserta.exam.show', [$session->id, 1]);
    }

    /**
     * Tampilkan soal ujian berdasarkan nomor.
     */
    public function show(ExamSession $session, int $nomor)
    {
        // Pastikan sesi milik user yang login
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        // Cek sesi masih berlangsung
        if ($session->status !== 'berlangsung') {
            return redirect()->route('peserta.exam.result', $session->result);
        }

        // Cek apakah waktu sudah habis
        if ($session->isExpired()) {
            $this->processSubmit($session, 'timeout');
            return redirect()->route('peserta.exam.result', $session->result)->with('info', 'Waktu habis! Ujian otomatis dikumpulkan.');
        }

        $session->load('tryoutPackage');

        // Gunakan urutan acak yang tersimpan, atau fallback ke urutan default
        $soalOrder = $session->soal_order;
        if (!empty($soalOrder)) {
            // Ambil soal berdasarkan urutan acak yang tersimpan
            $questionsById = $session->tryoutPackage->questions()->get()->keyBy('id');
            $questions = collect($soalOrder)->map(fn($id) => $questionsById->get($id))->filter()->values();
        } else {
            $questions = $session->tryoutPackage->questions;
        }

        $totalSoal = $questions->count();
        $nomor     = max(1, min($nomor, $totalSoal));
        $question  = $questions[$nomor - 1];

        // Load semua jawaban untuk navigasi
        $answers = ExamAnswer::where('exam_session_id', $session->id)
            ->get()
            ->keyBy('question_id');

        $currentAnswer = $answers->get($question->id);

        $isSEB = str_contains(request()->header('User-Agent'), 'SafeExamBrowser') || request()->hasHeader('X-SafeExamBrowser-RequestHash');

        return view('peserta.exam.show', compact(
            'session', 'question', 'questions', 'answers', 'currentAnswer', 'nomor', 'totalSoal', 'isSEB'
        ));
    }

    /**
     * Simpan jawaban soal (AJAX).
     */
    public function saveAnswer(Request $request, ExamSession $session)
    {
        if ($session->user_id !== Auth::id() || $session->status !== 'berlangsung') {
            return response()->json(['success' => false, 'message' => 'Sesi tidak valid.'], 403);
        }

        if ($session->isExpired()) {
            $this->processSubmit($session, 'timeout');
            return response()->json(['success' => false, 'message' => 'Waktu habis.', 'redirect' => route('peserta.exam.result', $session->result)], 200);
        }

        $request->validate([
            'question_id' => 'required|exists:questions,id',
            'jawaban'     => 'nullable|in:A,B,C,D,E',
        ]);

        ExamAnswer::updateOrCreate(
            ['exam_session_id' => $session->id, 'question_id' => $request->question_id],
            ['jawaban' => $request->jawaban]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Toggle penanda ragu-ragu (AJAX).
     */
    public function toggleRagu(Request $request, ExamSession $session)
    {
        if ($session->user_id !== Auth::id() || $session->status !== 'berlangsung') {
            return response()->json(['success' => false], 403);
        }

        $request->validate(['question_id' => 'required|exists:questions,id']);

        $answer = ExamAnswer::where('exam_session_id', $session->id)
            ->where('question_id', $request->question_id)
            ->first();

        if ($answer) {
            $answer->update(['is_ragu' => !$answer->is_ragu]);
            return response()->json(['success' => true, 'is_ragu' => $answer->is_ragu]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Submit ujian (manual atau auto dari JS).
     */
    public function submit(Request $request, ExamSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        if ($session->status !== 'berlangsung') {
            return redirect()->route('peserta.exam.result', $session->result);
        }

        $result = $this->processSubmit($session, 'selesai');

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'redirect' => route('peserta.exam.result', $result)]);
        }

        return redirect()->route('peserta.exam.result', $result)->with('success', 'Ujian berhasil dikumpulkan!');
    }

    /**
     * Catat pelanggaran peserta (AJAX).
     */
    public function logViolation(Request $request, ExamSession $session)
    {
        if ($session->user_id !== Auth::id() || $session->status !== 'berlangsung') {
            return response()->json(['success' => false, 'message' => 'Sesi tidak valid.'], 403);
        }

        $request->validate([
            'tipe' => 'required|string|in:fullscreen_exit,tab_switch,window_blur',
        ]);

        // Insert into structured exam_violations table
        $session->violations()->create([
            'violation_type' => $request->tipe,
        ]);

        $logs = $session->violation_logs ?? [];
        $logs[] = [
            'waktu' => Carbon::now()->toDateTimeString(),
            'tipe'  => $request->tipe,
        ];

        $session->violations_count = ($session->violations_count ?? 0) + 1;
        $session->violation_logs = $logs;
        $session->save();

        $limit = 3; // Batas pelanggaran
        $autoSubmit = $session->violations_count >= $limit;

        $resultId = null;
        if ($autoSubmit) {
            $result = $this->processSubmit($session, 'kecurangan');
            $resultId = $result->id;
        }

        return response()->json([
            'success'          => true,
            'violations_count' => $session->violations_count,
            'auto_submit'      => $autoSubmit,
            'result_id'        => $resultId,
            'redirect'         => $autoSubmit ? route('peserta.exam.result', $resultId) : null,
        ]);
    }

    /**
     * Proses kalkulasi nilai dan simpan ke results.
     */
    private function processSubmit(ExamSession $session, string $status): Result
    {
        $session->load(['tryoutPackage', 'answers.question.questionCode', 'answers.question.category']);

        $ended   = Carbon::now();
        $elapsed = $ended->diffInSeconds($session->started_at);

        $session->update([
            'status'       => $status,
            'ended_at'     => $ended,
            'durasi_detik' => $elapsed,
        ]);

        $benar = $salah = $kosong = 0;
        $skorTotal = 0;

        // Group the answers by questionCode
        $answersByCode = [];
        foreach ($session->answers as $answer) {
            $code = $answer->question->questionCode;
            if (!$code) {
                continue;
            }
            
            $codeId = $code->id;
            if (!isset($answersByCode[$codeId])) {
                $answersByCode[$codeId] = [
                    'code' => $code->code,
                    'name' => $code->name,
                    'answers' => []
                ];
            }
            $answersByCode[$codeId]['answers'][] = $answer;
        }

        $isSkd = ($session->tryoutPackage->group === 'SKD');
        $codeBreakdown = [];

        foreach ($answersByCode as $codeId => $data) {
            $codeVal = $data['code'];
            $codeName = $data['name'];
            $codeBenar = 0;
            $codeSalah = 0;
            $codeKosong = 0;
            $codePoints = 0;
            $totalCodeQuestions = count($data['answers']);

            foreach ($data['answers'] as $ans) {
                if (is_null($ans->jawaban)) {
                    $kosong++;
                    $codeKosong++;
                } else {
                    $isCorrect = $ans->isBenar();
                    if ($isCorrect) {
                        $benar++;
                        $codeBenar++;
                        if ($isSkd) {
                            $codePoints += 5;
                        }
                    } else {
                        $salah++;
                        $codeSalah++;
                        if ($isSkd && $codeVal === 'TKP') {
                            $visualKey = $ans->jawaban;
                            $mapping = $ans->options_mapping;
                            $originalKey = ($mapping && isset($mapping[$visualKey])) ? $mapping[$visualKey] : $visualKey;
                            $codePoints += (1 + (crc32($ans->question_id . $originalKey) % 4));
                        }
                    }
                }
            }

            if ($isSkd) {
                if ($codeVal !== 'TKP') {
                    $codePoints = $codeBenar * 5;
                }
                $codeScore = $codePoints;
                $skorTotal += $codeScore;
            } else {
                $codeScore = $totalCodeQuestions > 0 ? round(($codeBenar / $totalCodeQuestions) * 100, 2) : 0;
                $skorTotal += $codeScore;
            }

            $codeBreakdown[$codeId] = [
                'name'  => $codeName,
                'kode'  => $codeVal,
                'score' => $codeScore,
                'benar' => $codeBenar,
                'salah' => $codeSalah,
                'kosong'=> $codeKosong,
                'total' => $totalCodeQuestions,
            ];
        }

        $skorTwk = 0;
        $skorTiu = 0;
        $skorTkp = 0;
        foreach ($codeBreakdown as $b) {
            if ($b['kode'] === 'TWK') $skorTwk = $b['score'];
            if ($b['kode'] === 'TIU') $skorTiu = $b['score'];
            if ($b['kode'] === 'TKP') $skorTkp = $b['score'];
        }

        // For attempt score saving: if SNBT, we can save the average or sum
        $totalSoal  = $session->answers->count();
        $skorAttempt = $skorTotal; 

        // Update package attempt
        $attempt = \App\Models\PackageAttempt::where('participant_id', $session->user_id)
            ->where('package_id', $session->tryout_package_id)
            ->whereNull('finished_at')
            ->first();
        if ($attempt) {
            $attempt->update([
                'finished_at' => $ended,
                'score'       => $skorAttempt,
            ]);
        }

        return Result::create([
            'exam_session_id'    => $session->id,
            'user_id'            => $session->user_id,
            'tryout_package_id'  => $session->tryout_package_id,
            'skor_twk'           => $skorTwk,
            'skor_tiu'           => $skorTiu,
            'skor_tkp'           => $skorTkp,
            'skor_total'         => $skorTotal,
            'jumlah_benar'       => $benar,
            'jumlah_salah'       => $salah,
            'jumlah_kosong'      => $kosong,
            'category_scores'    => $codeBreakdown, // save structured code scores here
        ]);
    }

    /**
     * Halaman hasil ujian.
     */
    public function result(Result $result)
    {
        if ($result->user_id !== Auth::id()) {
            abort(403);
        }

        $result->load(['examSession.answers.question.questionCode', 'examSession.answers.question.category', 'examSession.answers.question.subCategory', 'tryoutPackage', 'user']);
        return view('peserta.exam.result', compact('result'));
    }

    public function downloadSebConfig(TryoutPackage $package)
    {
        $startUrl = $package->seb_url ?: route('peserta.exam.start', $package);
        $hashedQuitPassword = '';
        if ($package->seb_quit_password) {
            $hashedQuitPassword = hash('sha256', $package->seb_quit_password);
        }

        $allowReload = !$package->seb_browser_lockdown;
        $showUrl = !$package->seb_browser_lockdown;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">' . "\n";
        $xml .= '<plist version="1.0">' . "\n";
        $xml .= '<dict>' . "\n";
        $xml .= '    <key>startURL</key>' . "\n";
        $xml .= '    <string>' . htmlspecialchars($startUrl) . '</string>' . "\n";
        $xml .= '    <key>allowQuit</key>' . "\n";
        $xml .= '    <true/>' . "\n";
        $xml .= '    <key>quitURLConfirm</key>' . "\n";
        $xml .= '    <true/>' . "\n";
        if ($hashedQuitPassword) {
            $xml .= '    <key>hashedQuitPassword</key>' . "\n";
            $xml .= '    <string>' . strtoupper($hashedQuitPassword) . '</string>' . "\n";
        }
        $xml .= '    <key>browserWindowShowURL</key>' . "\n";
        $xml .= $showUrl ? '    <true/>' . "\n" : '    <false/>' . "\n";
        $xml .= '    <key>browserWindowShowReload</key>' . "\n";
        $xml .= $allowReload ? '    <true/>' . "\n" : '    <false/>' . "\n";
        $xml .= '    <key>allowPreferences</key>' . "\n";
        $xml .= '    <false/>' . "\n";
        $xml .= '    <key>sendBrowserExamKey</key>' . "\n";
        $xml .= '    <true/>' . "\n";
        $xml .= '</dict>' . "\n";
        $xml .= '</plist>';

        $fileName = str_replace(' ', '_', strtolower($package->nama)) . '.seb';

        return response($xml, 200, [
            'Content-Type' => 'application/x-safeexambrowser-config',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}
