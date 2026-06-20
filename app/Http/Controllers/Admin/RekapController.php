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

        $results  = $query->latest()->paginate(20)->withQueryString();
        $packages = TryoutPackage::all();

        return view('admin.rekap.index', compact('results', 'packages'));
    }

    public function exportCsv(Request $request)
    {
        $query = Result::with(['user', 'tryoutPackage']);

        if ($request->filled('tryout_package_id')) {
            $query->where('tryout_package_id', $request->tryout_package_id);
        }

        $results = $query->latest()->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="rekap_nilai_' . now()->format('Ymd_His') . '.csv"',
        ];

        $callback = function () use ($results) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Nama', 'No Peserta', 'Email', 'Paket Tryout', 'Skor TWK', 'Skor TIU', 'Skor TKP', 'Skor Total', 'Benar', 'Salah', 'Kosong', 'Status', 'Waktu Selesai']);

            foreach ($results as $i => $result) {
                fputcsv($file, [
                    $i + 1,
                    $result->user->name,
                    $result->user->no_peserta ?? '-',
                    $result->user->email,
                    $result->tryoutPackage->nama,
                    $result->skor_twk,
                    $result->skor_tiu,
                    $result->skor_tkp,
                    $result->skor_total,
                    $result->jumlah_benar,
                    $result->jumlah_salah,
                    $result->jumlah_kosong,
                    $result->examSession->status,
                    $result->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
