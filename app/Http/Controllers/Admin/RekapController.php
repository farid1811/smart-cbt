<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\TryoutPackage;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $query = Result::with(['user', 'tryoutPackage', 'examSession']);

        if ($request->filled('tryout_package_id')) {
            $query->where('tryout_package_id', $request->tryout_package_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('username', 'like', "%{$search}%");
                })->orWhereHas('tryoutPackage', function($pq) use ($search) {
                    $pq->where('nama', 'like', "%{$search}%");
                });
            });
        }

        $results  = $query->latest()->paginate(25)->withQueryString();
        $packages = TryoutPackage::all();

        // Calculate rankings for the displayed results
        $allResultsForRanking = Result::all()->groupBy('tryout_package_id');
        $rankingsMap = [];
        foreach ($allResultsForRanking as $pkgId => $group) {
            $sorted = $group->sortByDesc('skor_total')->values();
            foreach ($sorted as $index => $res) {
                $rankingsMap[$res->id] = $index + 1;
            }
        }

        return view('admin.rekap.index', compact('results', 'packages', 'rankingsMap'));
    }

    public function exportCsv(Request $request)
    {
        $query = Result::with(['user', 'tryoutPackage', 'examSession']);

        if ($request->filled('tryout_package_id')) {
            $query->where('tryout_package_id', $request->tryout_package_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('username', 'like', "%{$search}%");
                })->orWhereHas('tryoutPackage', function($pq) use ($search) {
                    $pq->where('nama', 'like', "%{$search}%");
                });
            });
        }

        $results = $query->get();

        // Calculate per-package rankings across all database results
        $allResultsForRanking = Result::all()->groupBy('tryout_package_id');
        $rankingsMap = [];
        foreach ($allResultsForRanking as $pkgId => $group) {
            $sorted = $group->sortByDesc('skor_total')->values();
            foreach ($sorted as $index => $res) {
                $rankingsMap[$res->id] = $index + 1;
            }
        }

        $headers = [
            'Content-Type'        => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="rekap_nilai_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($results, $rankingsMap) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM to ensure seamless MS Excel opening
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'Participant Name',
                'Username',
                'Group',
                'Package',
                'TWK Score',
                'TIU Score',
                'TKP Score',
                'Total Score',
                'Ranking'
            ]);

            foreach ($results as $result) {
                fputcsv($file, [
                    $result->user->name,
                    $result->user->username ?? '-',
                    $result->tryoutPackage->group ?? '-',
                    $result->tryoutPackage->nama,
                    $result->skor_twk,
                    $result->skor_tiu,
                    $result->skor_tkp,
                    $result->skor_total,
                    $rankingsMap[$result->id] ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function detail(Result $result)
    {
        $result->load(['user', 'tryoutPackage', 'examSession']);
        $session = $result->examSession;
        
        $answers = \App\Models\ExamAnswer::where('exam_session_id', $session->id)
            ->with(['question.category', 'question.questionCode'])
            ->get();
            
        $soalOrder = $session->soal_order;
        if (!empty($soalOrder)) {
            $answers = $answers->sortBy(function($ans) use ($soalOrder) {
                $pos = array_search($ans->question_id, $soalOrder);
                return $pos !== false ? $pos : 9999;
            })->values();
        } else {
            $answers = $answers->sortBy('question.urutan')->values();
        }

        $incorrectAnswers = $answers->filter(fn($ans) => !$ans->isBenar());
        $categoryErrors = [];
        foreach ($incorrectAnswers as $ans) {
            $catName = $ans->question->category->name ?? 'Tanpa Kategori';
            if (!isset($categoryErrors[$catName])) {
                $categoryErrors[$catName] = 0;
            }
            $categoryErrors[$catName]++;
        }
        arsort($categoryErrors);

        return view('admin.rekap.detail', compact('result', 'answers', 'categoryErrors'));
    }
}
