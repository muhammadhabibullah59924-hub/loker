<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardRequest;
use App\Models\Job;
use App\Services\DistanceService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

/**
 * Dasbor loker terurut dari yang terdekat (F2, lihat docs/PRD.md).
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan loker aktif terurut menaik sesuai jarak dari lokasi pengguna.
     *
     * Seluruh logika jarak berada di DistanceService, bukan di sini
     * (lihat AGENTS.md dan docs/CODE_STYLE.md).
     */
    public function index(DashboardRequest $request, DistanceService $jarak): View
    {
        $pengguna = $request->lokasiPengguna();

        $titik = $pengguna ?? [
            'lat' => (float) config('loker.lokasi_default.lat'),
            'lng' => (float) config('loker.lokasi_default.lng'),
        ];

        $query = Job::query()
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where(function ($queryKadaluarsa) {
                // Loker yang batas waktunya sudah lewat dianggap kadaluarsa
                // dan tidak ditampilkan di dasbor (T-015).
                $queryKadaluarsa->whereNull('deadline')
                    ->orWhereDate('deadline', '>=', today());
            });

        // Pencarian nama PT atau divisi hanya menampilkan yang cocok (F5).
        $kataKunci = $request->input('q');

        if ($kataKunci !== null && $kataKunci !== '') {
            $query->where(function ($queryPencarian) use ($kataKunci) {
                $queryPencarian->where('company_name', 'like', '%'.$kataKunci.'%')
                    ->orWhere('division', 'like', '%'.$kataKunci.'%');
            });
        }

        // Filter tipe loker: hanya tampilkan tipe yang dipilih (T-030).
        $tipe = $request->input('tipe');

        if ($tipe !== null && $tipe !== '') {
            $query->where('job_type', $tipe);
        }

        // Filter divisi lewat chip di dasbor (T-032).
        $divisi = $request->input('divisi');

        if ($divisi !== null && $divisi !== '') {
            $query->where('division', $divisi);
        }

        $lokers = $query
            ->get()
            ->map(fn (Job $loker): array => [
                'loker' => $loker,
                'jarak' => $jarak->distanceInKm(
                    $titik['lat'],
                    $titik['lng'],
                    (float) $loker->latitude,
                    (float) $loker->longitude,
                ),
            ]);

        // Urutan daftar loker (T-031): terdekat (default), terbaru, atau butuh terbanyak.
        $urut = $request->input('urut');

        $lokers = match ($urut) {
            'terbaru' => $lokers->sortByDesc(fn (array $item) => $item['loker']->created_at)->values(),
            'butuh' => $lokers->sortByDesc(fn (array $item) => $item['loker']->positions_needed)->values(),
            default => $lokers->sortBy('jarak')->values(),
        };

        // Filter radius memakai hasil perhitungan jarak, supaya urutan tetap menaik.
        $radius = $request->input('radius');

        if ($radius !== null) {
            $lokers = $lokers->filter(
                fn (array $item) => $item['jarak'] <= (float) $radius
            )->values();
        }

        // T-053: bagi daftar menjadi 20 loker per halaman. Pemotongan dilakukan
        // setelah filter dan urutan jarak diterapkan, karena keduanya dihitung
        // di PHP sehingga tidak bisa memakai paginate() langsung dari query.
        $perHalaman = 20;
        $halamanMaksimal = max(1, (int) ceil($lokers->count() / $perHalaman));
        $halaman = min(max(1, (int) $request->input('page', 1)), $halamanMaksimal);

        $paginator = new LengthAwarePaginator(
            $lokers->forPage($halaman, $perHalaman),
            $lokers->count(),
            $perHalaman,
            $halaman,
            ['path' => route('dasbor')],
        );

        // Seluruh filter aktif ikut terbawa saat berpindah halaman.
        $paginator->withQueryString();

        // Loker populer: loker aktif, berkoordinat, belum kadaluarsa, dan butuh
        // lebih dari satu orang, diurut dari yang butuh paling banyak (T-020).
        // T-052: bagian ini ikut mematuhi filter yang sedang aktif supaya tidak
        // menampilkan loker bertipe/divisi lain di bawah filter dasbor.
        $queryPopuler = Job::query()
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where(function ($queryKadaluarsa) {
                $queryKadaluarsa->whereNull('deadline')
                    ->orWhereDate('deadline', '>=', today());
            })
            ->where('positions_needed', '>', 1);

        if ($kataKunci !== null && $kataKunci !== '') {
            $queryPopuler->where(function ($queryPencarian) use ($kataKunci) {
                $queryPencarian->where('company_name', 'like', '%'.$kataKunci.'%')
                    ->orWhere('division', 'like', '%'.$kataKunci.'%');
            });
        }

        if ($tipe !== null && $tipe !== '') {
            $queryPopuler->where('job_type', $tipe);
        }

        if ($divisi !== null && $divisi !== '') {
            $queryPopuler->where('division', $divisi);
        }

        $lokerPopuler = $queryPopuler
            ->orderByDesc('positions_needed')
            ->limit(6)
            ->get()
            ->map(fn (Job $loker): array => [
                'loker' => $loker,
                'jarak' => $jarak->distanceInKm(
                    $titik['lat'],
                    $titik['lng'],
                    (float) $loker->latitude,
                    (float) $loker->longitude,
                ),
            ]);

        if ($radius !== null) {
            $lokerPopuler = $lokerPopuler
                ->filter(fn (array $item): bool => $item['jarak'] <= (float) $radius)
                ->values();
        }

        // Daftar divisi aktif untuk chip filter di dasbor (T-032).
        $divisiTersedia = Job::query()
            ->where('is_active', true)
            ->whereNotNull('division')
            ->distinct()
            ->orderBy('division')
            ->pluck('division');

        // Jumlah loker aktif tersedia dipakai untuk menampilkan statistik di dasbor (T-022).
        $jumlahLokerAktif = (int) Job::where('is_active', true)->count();

        // Jumlah loker populer dipakai untuk statistik tambahan di dasbor (T-025).
        $jumlahLokerPopuler = (int) Job::where('is_active', true)->where('positions_needed', '>', 1)->count();

        // Statistik loker premium dan divisi (T-048). Dihitung dari data yang
        // dimuat, supaya tetap aman bila migrasi is_premium belum dijalankan.
        $jumlahLokerPremium = Job::where('is_active', true)->get()
            ->filter(fn (Job $loker): bool => (bool) $loker->is_premium)
            ->count();

        return view('dashboard', [
            'lokers' => $paginator,
            'paginator' => $paginator,
            'loker_populer' => $lokerPopuler,
            'dari_lokasi_pengguna' => $pengguna !== null,
            'lokasi_default' => config('loker.lokasi_default.nama'),
            'q' => $request->input('q'),
            'radius' => $request->input('radius'),
            'tipe' => $request->input('tipe'),
            'urut' => $request->input('urut'),
            'divisi' => $request->input('divisi'),
            'divisiTersedia' => $divisiTersedia,
            'titik' => $titik,
            'jumlahLokerAktif' => $jumlahLokerAktif,
            'jumlahLokerPopuler' => $jumlahLokerPopuler,
            'jumlahLokerPremium' => $jumlahLokerPremium,
            'jumlahDivisi' => $divisiTersedia->count(),
        ]);
    }
}
