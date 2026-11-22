<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder data awal: 1 admin, 1 pencari kerja, dan 260 loker di Kota Probolinggo
 * (30 loker rinci + 230 loker turunan yang mencakup seluruh divisi).
 *
 * Koordinat adalah data dummy perkiraan di dalam wilayah Kota Probolinggo
 * (memakai tujuh kecamatan sebagai titik acuan) — dipakai untuk menguji
 * urutan jarak dan peta, bukan data lokasi resmi.
 *
 * Kata sandi di bawah adalah kredensial demo lokal, bukan rahasia.
 */
class LokerSeeder extends Seeder
{
    /**
     * Akun demo.
     */
    private const ADMIN_EMAIL = 'admin@lokerdekat.test';

    private const USER_EMAIL = 'pencari@lokerdekat.test';

    private const DEMO_PASSWORD = 'password';

    /**
     * Jalankan seeder.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => self::ADMIN_EMAIL],
            [
                'name' => 'Admin LokerDekat',
                'password' => Hash::make(self::DEMO_PASSWORD),
                'role' => 'admin',
            ],
        );

        User::updateOrCreate(
            ['email' => self::USER_EMAIL],
            [
                'name' => 'Budi Pencari Kerja',
                'password' => Hash::make(self::DEMO_PASSWORD),
                'role' => 'user',
            ],
        );

        $kolomTersedia = array_flip($this->kolomJobs());

        foreach (array_merge($this->lokernya(), $this->lokerPerDivisi()) as $loker) {
            Job::updateOrCreate(
                [
                    'company_name' => $loker['company_name'],
                    'division' => $loker['division'],
                ],
                array_intersect_key($loker, $kolomTersedia) + ['posted_by' => $admin->id],
            );
        }
    }

    /**
     * Daftar kolom tabel jobs yang benar-benar ada pada database aktif.
     *
     * Database lokal belum selalu termigrasi sampai kolom is_premium (T-024),
     * jadi seeder tidak boleh menulis kolom yang belum ada.
     *
     * @return array<int, string>
     */
    private function kolomJobs(): array
    {
        if (DB::getDriverName() === 'sqlite') {
            return DB::getSchemaBuilder()->getColumnListing('jobs');
        }

        return array_map(
            fn (object $kolom): string => $kolom->column_name,
            DB::select(
                'select column_name from information_schema.columns where table_schema = ? and table_name = ?',
                [DB::getDatabaseName(), 'jobs'],
            ),
        );
    }

    /**
     * Daftar 30 loker dummy di Kota Probolinggo.
     *
     * @return array<int, array<string, mixed>>
     */
    private function lokernya(): array
    {
        return [
            // Kecamatan Gading (utara)
            [
                'company_name' => 'PT Pilar Nusantara',
                'address' => 'Jl. Blandongan No. 12, Gading',
                'division' => 'Produksi',
                'city' => 'Kota Probolinggo',
                'description' => 'Operator lini produksi barang plastik rumah tangga.',
                'requirements' => 'Minimal SMA/SMK, siap kerja shift, jujur dan teliti.',
                'job_type' => 'Full-Time',
                'positions_needed' => 3,
                'is_premium' => true,
                'deadline' => '2026-11-20',
                'latitude' => -7.7004000,
                'longitude' => 113.2035000,
            ],
            [
                'company_name' => 'CV Karya Teknik Ringan',
                'address' => 'Jl. Untung Suropati No. 8, Gading',
                'division' => 'Teknisi',
                'city' => 'Kota Probolinggo',
                'description' => 'Teknisi perawatan mesin produksi dan peralatan bengkel.',
                'requirements' => 'SMK Teknik Mesin, paham kelistrikan dasar, memiliki SIM C.',
                'deadline' => null,
                'latitude' => -7.6978000,
                'longitude' => 113.2076000,
            ],
            [
                'company_name' => 'PT Bahari Segar Jaya',
                'address' => 'Jl. Raya Gading No. 45, Gading',
                'division' => 'Gudang',
                'city' => 'Kota Probolinggo',
                'description' => 'Penataan barang masuk dan keluar gudang hasil laut beku.',
                'requirements' => 'Minimal SMA, mampu mengangkat beban 20 kg, tertib administrasi.',
                'deadline' => '2026-11-10',
                'latitude' => -7.7039000,
                'longitude' => 113.1998000,
            ],
            // Kecamatan Kedopok (barat laut)
            [
                'company_name' => 'PT Surya Tekstil Jaya',
                'address' => 'Jl. Dr. Wahidin Sudirohusodo No. 33, Kedopok',
                'division' => 'Produksi',
                'city' => 'Kota Probolinggo',
                'description' => 'Operator jahit dan penjahit pakaian jadi.',
                'requirements' => 'Minimal SMA, mampu menjahit, pengalaman diutamakan.',
                'job_type' => 'Full-Time',
                'positions_needed' => 2,
                'deadline' => '2026-11-25',
                'latitude' => -7.7073000,
                'longitude' => 113.2148000,
            ],
            [
                'company_name' => 'CV Rumah Rasa Nusantara',
                'address' => 'Jl. KH. Agus Salim No. 19, Kedopok',
                'division' => 'Kasir',
                'city' => 'Kota Probolinggo',
                'description' => 'Kasir restoran dengan layanan kasir digital.',
                'requirements' => 'Minimal SMA, cekatan menghitung, siap kerja malam.',
                'deadline' => null,
                'latitude' => -7.7046000,
                'longitude' => 113.2103000,
            ],
            [
                'company_name' => 'PT Cahaya Elektrik Makmur',
                'address' => 'Jl. Diponegoro No. 51, Kedopok',
                'division' => 'Pemasaran',
                'city' => 'Kota Probolinggo',
                'description' => 'Penjual peralatan listrik ke toko dan proyek kecil.',
                'requirements' => 'Minimal SMA/SMK, terbiasa menawarkan produk, berjiwa sosial.',
                'deadline' => '2026-12-05',
                'latitude' => -7.7021000,
                'longitude' => 113.2179000,
            ],
            // Kecamatan Mayangan (pusat kota)
            [
                'company_name' => 'PT Probowangi Agung',
                'address' => 'Jl. Panglima Sudirman No. 21, Mayangan',
                'division' => 'Administrasi',
                'city' => 'Kota Probolinggo',
                'description' => 'Pengelolaan berkas kantor dan pendataan pelanggan.',
                'requirements' => 'Minimal SMA/SMK, mahir Excel, rapi dan disiplin.',
                'job_type' => 'Full-Time',
                'positions_needed' => 2,
                'deadline' => '2026-11-18',
                'latitude' => -7.7149000,
                'longitude' => 113.2163000,
            ],
            [
                'company_name' => 'PT Cerdas Digital Prima',
                'address' => 'Jl. Ahmad Yani No. 77, Mayangan',
                'division' => 'Teknologi Informasi',
                'city' => 'Kota Probolinggo',
                'description' => 'Perawatan jaringan komputer dan dukungan pengguna kantor.',
                'requirements' => 'SMK RPL/TKJ, paham jaringan dasar, siap layanan lokal.',
                'deadline' => '2026-11-30',
                'latitude' => -7.7312000,
                'longitude' => 113.2194000,
            ],
            [
                'company_name' => 'PT Griya Interior Cantik',
                'address' => 'Jl. Hos Cokroaminoto No. 5, Mayangan',
                'division' => 'Desain',
                'city' => 'Kota Probolinggo',
                'description' => 'Perancang gambar interior rumah dan kantor.',
                'requirements' => 'SMK Desain Komunikasi Visual, bisa CorelDraw/Photoshop.',
                'deadline' => null,
                'latitude' => -7.7276000,
                'longitude' => 113.2215000,
            ],
            [
                'company_name' => 'PT Aroma Kopi Tunggal',
                'address' => 'Jl. Suroso No. 14, Mayangan',
                'division' => 'Pelayanan Pelanggan',
                'city' => 'Kota Probolinggo',
                'description' => 'Barista dan pelayanan pelanggan kedai kopi.',
                'requirements' => 'Minimal SMA, senang bertemu orang, siap kerja akhir pekan.',
                'job_type' => 'Part-Time',
                'positions_needed' => 2,
                'deadline' => '2026-11-15',
                'latitude' => -7.7185000,
                'longitude' => 113.2139000,
            ],
            // Kecamatan Wonoasih (tengah selatan)
            [
                'company_name' => 'PT Mandiri Logistik Timur',
                'address' => 'Jl. Mayjen Sutoyo No. 9, Wonoasih',
                'division' => 'Pengiriman',
                'city' => 'Kota Probolinggo',
                'description' => 'Pengemudi dan pendamping pengiriman barang antar kota.',
                'requirements' => 'SIM B1, pengalaman 1 tahun, paham rute Jawa Timur.',
                'job_type' => 'Full-Time',
                'positions_needed' => 2,
                'deadline' => '2026-11-22',
                'latitude' => -7.7458000,
                'longitude' => 113.2246000,
            ],
            [
                'company_name' => 'CV Sumber Rejeki Motor',
                'address' => 'Jl. Slamet Riyadi No. 27, Wonoasih',
                'division' => 'Servis',
                'city' => 'Kota Probolinggo',
                'description' => 'Mekanik motor untuk servis rutin dan ganti sparepart.',
                'requirements' => 'SMK Otomotif, jujur, mampu bekerja dengan target.',
                'deadline' => null,
                'latitude' => -7.7487000,
                'longitude' => 113.2288000,
            ],
            [
                'company_name' => 'PT Sentosa Garmen Lestari',
                'address' => 'Jl. WR Supratman No. 62, Wonoasih',
                'division' => 'Quality Control',
                'city' => 'Kota Probolinggo',
                'description' => 'Pemeriksaan kualitas jahitan sebelum produk dikirim.',
                'requirements' => 'Minimal SMA, teliti, mampu membaca gambar teknik jahit.',
                'deadline' => '2026-12-01',
                'latitude' => -7.7431000,
                'longitude' => 113.2267000,
            ],
            [
                'company_name' => 'CV Roti Enak Nusantara',
                'address' => 'Jl. Pahlawan No. 3, Wonoasih',
                'division' => 'Produksi',
                'city' => 'Kota Probolinggo',
                'description' => 'Pembuatan roti harian untuk toko dan agen.',
                'requirements' => 'Minimal SMP/SMA, siap kerja pagi, sehat jasmani.',
                'deadline' => '2026-11-12',
                'latitude' => -7.7499000,
                'longitude' => 113.2221000,
            ],
            // Kecamatan Kanigaran (selatan)
            [
                'company_name' => 'PT Berkah Pangan Sejahtera',
                'address' => 'Jl. Teuku Umar No. 41, Kanigaran',
                'division' => 'Pengolahan',
                'city' => 'Kota Probolinggo',
                'description' => 'Pengolahan bahan pangan menjadi produk siap edar.',
                'requirements' => 'Minimal SMA, paham kebersihan pangan, siap kerja shift.',
                'deadline' => '2026-11-28',
                'latitude' => -7.7552000,
                'longitude' => 113.2345000,
            ],
            [
                'company_name' => 'PT Dunia Plastik Mandiri',
                'address' => 'Jl. Imam Bonjol No. 16, Kanigaran',
                'division' => 'Packing',
                'city' => 'Kota Probolinggo',
                'description' => 'Pengemasan produk plastik sebelum dikirim ke distributor.',
                'requirements' => 'Minimal SMP/SMA, cekatan tangan, bekerja sesuai SOP.',
                'deadline' => null,
                'latitude' => -7.7586000,
                'longitude' => 113.2378000,
            ],
            [
                'company_name' => 'PT Harapan Baru Kimia',
                'address' => 'Jl. Hasanudin No. 8, Kanigaran',
                'division' => 'Laboratorium',
                'city' => 'Kota Probolinggo',
                'description' => 'Pengujian kualitas bahan baku industri kecil.',
                'requirements' => 'SMK Analis/Teknik Kimia, teliti, paham alat ukur dasar.',
                'deadline' => '2026-12-10',
                'latitude' => -7.7521000,
                'longitude' => 113.2312000,
            ],
            // Kecamatan Karanganyar (timur)
            [
                'company_name' => 'PT Utama Karya Persada',
                'address' => 'Jl. Raya Karanganyar No. 55, Karanganyar',
                'division' => 'Administrasi Proyek',
                'city' => 'Kota Probolinggo',
                'description' => 'Pendataan proyek renovasi bangunan dan laporan harian.',
                'requirements' => 'Minimal SMA/SMK, mahir Word/Excel, siap survei lapangan.',
                'deadline' => '2026-11-24',
                'latitude' => -7.7184000,
                'longitude' => 113.2421000,
            ],
            [
                'company_name' => 'CV Tiga Saudara Metal',
                'address' => 'Jl. Ronggowulito No. 30, Karanganyar',
                'division' => 'Produksi',
                'city' => 'Kota Probolinggo',
                'description' => 'Pembuatan pagar, kanopi, dan rangka baja ringan.',
                'requirements' => 'SMK Las/sanggah, berpengalaman las argon, berhati-hati.',
                'deadline' => null,
                'latitude' => -7.7219000,
                'longitude' => 113.2457000,
            ],
            [
                'company_name' => 'PT Rapi Distribusi Utama',
                'address' => 'Jl. MT Haryono No. 71, Karanganyar',
                'division' => 'Pengiriman',
                'city' => 'Kota Probolinggo',
                'description' => 'Pengiriman barang ke toko grosir di Probolinggo Raya.',
                'requirements' => 'SIM C, paham rute kota, jujur dan tepat waktu.',
                'deadline' => '2026-11-19',
                'latitude' => -7.7156000,
                'longitude' => 113.2396000,
            ],
            [
                'company_name' => 'PT Global Kemasan Timur',
                'address' => 'Jl. Aloon Aloon No. 11, Karanganyar',
                'division' => 'Pemasaran',
                'city' => 'Kota Probolinggo',
                'description' => 'Pemasaran kemasan makanan ke pelaku usaha mikro.',
                'requirements' => 'Minimal SMA, komunikatif, siap kunjungan pelanggan.',
                'deadline' => '2026-11-27',
                'latitude' => -7.7248000,
                'longitude' => 113.2383000,
            ],
            // Kecamatan Pakuningratan (timur)
            [
                'company_name' => 'PT Mega Sarana Farmasi',
                'address' => 'Jl. Raya Pakuningratan No. 4, Pakuningratan',
                'division' => 'Administrasi Apotek',
                'city' => 'Kota Probolinggo',
                'description' => 'Pencatatan stok obat dan pelayanan resep sederhana.',
                'requirements' => 'SMK Farmasi/Administrasi, tertib data, jujur.',
                'deadline' => '2026-11-30',
                'latitude' => -7.7342000,
                'longitude' => 113.2468000,
            ],
            [
                'company_name' => 'PT Anugerah Bengkel Las',
                'address' => 'Jl. Sutomo No. 18, Pakuningratan',
                'division' => 'Teknisi',
                'city' => 'Kota Probolinggo',
                'description' => 'Perakitan dan perbaikan konstruksi logam.',
                'requirements' => 'SMK Teknik, paham gambar kerja, siap kerja lapangan.',
                'deadline' => null,
                'latitude' => -7.7377000,
                'longitude' => 113.2493000,
            ],
            [
                'company_name' => 'CV Sejahtera Otomotif',
                'address' => 'Jl. Hayam Wuruk No. 66, Pakuningratan',
                'division' => 'Penjualan',
                'city' => 'Kota Probolinggo',
                'description' => 'Penjualan suku cadang mobil dan konsultasi pemesanan.',
                'requirements' => 'Minimal SMA, paham jenis suku cadang, ramah.',
                'deadline' => '2026-12-08',
                'latitude' => -7.7316000,
                'longitude' => 113.2437000,
            ],
            // Tambahan loker (T-017): kelanjutan data dummy untuk dasbor.
            [
                'company_name' => 'PT Sentra Boga Nusantara',
                'address' => 'Jl. Cendekia No. 9, Gading',
                'division' => 'Pengolahan',
                'city' => 'Kota Probolinggo',
                'description' => 'Pengolahan bahan pangan menjadi produk siap saji untuk ritel modern.',
                'requirements' => 'Minimal SMA/SMK, paham food safety, siap kerja shift pagi.',
                'deadline' => '2026-12-15',
                'opens_at' => '2026-11-12',
                'closes_at' => '2026-12-15',
                'latitude' => -7.6935000,
                'longitude' => 113.2010000,
            ],
            [
                'company_name' => 'CV Dapur Sejahtera',
                'address' => 'Jl. Raya Gading No. 77, Gading',
                'division' => 'Pelayanan',
                'city' => 'Kota Probolinggo',
                'description' => 'Kuliner dengan menu segar lokal dan catering harian.',
                'requirements' => 'Minimal SMA, kreatif, siap kerja tim.',
                'deadline' => '2026-12-18',
                'opens_at' => '2026-11-15',
                'closes_at' => '2026-12-18',
                'latitude' => -7.6990000,
                'longitude' => 113.2055000,
            ],
            [
                'company_name' => 'PT Mitra Logistik Sejahtera',
                'address' => 'Jl. Raya Kedopok No. 101, Kedopok',
                'division' => 'Logistik',
                'city' => 'Kota Probolinggo',
                'description' => 'Pengiriman barang dalam kota dengan armada mini dan dokumentasi rute.',
                'requirements' => 'SIM B1, berpengalaman, jujur.',
                'deadline' => '2026-12-20',
                'opens_at' => '2026-11-18',
                'closes_at' => '2026-12-20',
                'latitude' => -7.7028000,
                'longitude' => 113.2089000,
            ],
            [
                'company_name' => 'CV Arsitek Nusantara',
                'address' => 'Jl. Diponegoro No. 88, Kedopok',
                'division' => 'Konstruksi',
                'city' => 'Kota Probolinggo',
                'description' => 'Perencanaan dan konstruksi renovasi interior kantor.',
                'requirements' => 'SMK Arsitek, bisa AutoCAD, pengalaman proyek minimal 2.',
                'deadline' => '2026-12-22',
                'opens_at' => '2026-11-20',
                'closes_at' => '2026-12-22',
                'latitude' => -7.7050000,
                'longitude' => 113.2123000,
            ],
            [
                'company_name' => 'PT Dana Karya Sentosa',
                'address' => 'Jl. Merdeka No. 45, Mayangan',
                'division' => 'Keuangan',
                'city' => 'Kota Probolinggo',
                'description' => 'Pelayanan pembukuan, perpajakan kecil, dan konsultasi keuangan UMKM.',
                'requirements' => 'Minimal SMA/SMK, paham akuntansi dasar.',
                'deadline' => '2026-12-25',
                'opens_at' => '2026-11-22',
                'closes_at' => '2026-12-25',
                'latitude' => -7.7100000,
                'longitude' => 113.2150000,
            ],
            [
                'company_name' => 'CV Kreatif Media Nusantara',
                'address' => 'Jl. Suroso No. 27, Mayangan',
                'division' => 'Kreatif',
                'city' => 'Kota Probolinggo',
                'description' => 'Perancang kampanye media sosial dan desain konten merek lokal.',
                'requirements' => 'SMK Desain/Kreatif, bisa Adobe suite, siap kolaborasi.',
                'deadline' => '2026-12-28',
                'opens_at' => '2026-11-25',
                'closes_at' => '2026-12-28',
                'latitude' => -7.7160000,
                'longitude' => 113.2180000,
            ],
        ];
    }

    /**
     * Bangkitkan 10 loker untuk setiap divisi agar seluruh divisi terwakili.
     *
     * Data sengaja deterministik dan fiktif: aman dijalankan ulang karena
     * memakai updateOrCreate dengan pasangan nama PT dan divisi sebagai kunci.
     *
     * @return array<int, array<string, mixed>>
     */
    private function lokerPerDivisi(): array
    {
        $divisi = [
            'Administrasi' => ['Mengurus surat, arsip, dan jadwal kantor.', 'Minimal SMA, rapi, dan teliti.'],
            'Administrasi Apotek' => ['Mengelola resep, stok obat, dan administrasi apotek.', 'Minimal SMK Farmasi, tertib data.'],
            'Administrasi Proyek' => ['Menyiapkan dokumen dan jadwal pelaksanaan proyek.', 'Minimal SMA, paham Microsoft Office.'],
            'Desain' => ['Membuat desain grafis untuk kebutuhan produksi dan promosi.', 'SMK Desain, menguasai perangkat desain.'],
            'Gudang' => ['Menata barang masuk, keluar, dan stok gudang.', 'Minimal SMA, mampu mengangkat beban 20 kg.'],
            'Kasir' => ['Melayani pembayaran dan merekap transaksi harian.', 'Minimal SMA, teliti dan ramah.'],
            'Keuangan' => ['Membantu pembukuan, kas, dan laporan keuangan.', 'Minimal SMA/SMK, paham akuntansi dasar.'],
            'Konstruksi' => ['Membantu pekerjaan lapangan dan perakitan struktur.', 'Minimal SMA, disiplin, dan memakai APD.'],
            'Kreatif' => ['Menyusun ide konten dan kampanye untuk merek lokal.', 'Kreatif, bisa menulis, dan siap kolaborasi.'],
            'Laboratorium' => ['Melakukan pengujian sampel dan mencatat hasilnya.', 'Minimal SMK Kimia/Laboratorium, teliti.'],
            'Logistik' => ['Mengatur distribusi barang dan dokumen pengiriman.', 'Minimal SMA, rapi, dan komunikatif.'],
            'Packing' => ['Membungkus dan memberi label produk sebelum dikirim.', 'Minimal SMA, teliti, dan cepat.'],
            'Pelayanan' => ['Melayani kebutuhan pelanggan di lokasi usaha.', 'Minimal SMA, ramah, dan sabar.'],
            'Pelayanan Pelanggan' => ['Menjawab pertanyaan dan menangani keluhan pelanggan.', 'Minimal SMA, komunikatif, dan sabar.'],
            'Pemasaran' => ['Mencari peluang pasar dan mempromosikan produk.', 'Minimal SMA, komunikatif, dan proaktif.'],
            'Pengiriman' => ['Mengantar paket dan memastikan barang sampai tepat waktu.', 'Memiliki SIM C, jujur, dan tepat waktu.'],
            'Pengolahan' => ['Menjaga proses pengolahan bahan menjadi produk jadi.', 'Minimal SMK, disiplin, dan paham kebersihan.'],
            'Penjualan' => ['Menawarkan produk dan mencapai target penjualan.', 'Minimal SMA, komunikatif, dan target oriented.'],
            'Produksi' => ['Menjaga lini produksi agar berjalan lancar dan aman.', 'Minimal SMA/SMK, siap kerja shift.'],
            'Quality Control' => ['Memeriksa kualitas produk sesuai standar perusahaan.', 'Minimal SMK, teliti, dan paham standar mutu.'],
            'Servis' => ['Memperbaiki dan merawat peralatan pelanggan.', 'SMK Teknik, paham dasar kelistrikan.'],
            'Teknisi' => ['Memasang, memeriksa, dan memelihara peralatan.', 'SMK Teknik, memiliki SIM C menjadi nilai tambah.'],
            'Teknologi Informasi' => ['Membantu pengelolaan sistem, jaringan, dan data.', 'Minimal SMK/S1 IT, paham dasar komputer.'],
        ];

        $perusahaan = [
            'Surya Abadi', 'Bumi Lestari', 'Rizky Jaya', 'Mekar Sari', 'Nusantara Sejahtera',
            'Mandiri Putra', 'Cahaya Timur', 'Tirta Wijaya', 'Gerbang Mas', 'Pusaka Raya',
        ];

        $jalan = [
            'Pahlawan', 'Merdeka', 'Sudirman', 'Diponegoro', 'Ahmad Yani',
            'Gatot Subroto', 'Suroso', 'Blandongan', 'Raya', 'Kenanga',
        ];

        $lokasi = [
            ['Gading', -7.7004, 113.2035],
            ['Kedopok', -7.7260, 113.1905],
            ['Mayangan', -7.7138, 113.2150],
            ['Wonoasih', -7.7265, 113.2220],
            ['Kanigaran', -7.7350, 113.2155],
            ['Karanganyar', -7.7200, 113.2300],
            ['Pakuningratan', -7.7150, 113.2250],
        ];

        $tipe = ['Full-Time', 'Part-Time', 'Contract', 'Internship'];
        $hariIni = now();
        $loker = [];
        $indeksDivisi = 0;

        foreach ($divisi as $namaDivisi => $deskripsiDivisi) {
            for ($i = 0; $i < 10; $i++) {
                $tempat = $lokasi[($indeksDivisi + $i) % count($lokasi)];
                $batas = $hariIni->copy()->addDays(30 + (($indeksDivisi * 3 + $i) % 60))->toDateString();

                $loker[] = [
                    'company_name' => ($i % 2 === 0 ? 'PT ' : 'CV ').$perusahaan[$i].' '.$namaDivisi,
                    'address' => 'Jl. '.$jalan[$i].' No. '.(10 + $indeksDivisi + $i * 2).', '.$tempat[0],
                    'division' => $namaDivisi,
                    'city' => 'Kota Probolinggo',
                    'description' => $deskripsiDivisi[0].' Berkontribusi pada tim '.$namaDivisi.' di '.$tempat[0].'.',
                    'requirements' => $deskripsiDivisi[1].' Siap bekerja di Kota Probolinggo.',
                    'job_type' => $tipe[($indeksDivisi + $i) % count($tipe)],
                    'positions_needed' => 1 + (($indeksDivisi + $i) % 5),
                    'is_premium' => ($indeksDivisi + $i) % 7 === 0,
                    'is_active' => true,
                    'deadline' => $batas,
                    'opens_at' => $hariIni->toDateString(),
                    'closes_at' => $batas,
                    'latitude' => round($tempat[1] + (($i % 3) - 1) * 0.0015, 7),
                    'longitude' => round($tempat[2] + (($indeksDivisi % 3) - 1) * 0.0018, 7),
                ];
            }

            $indeksDivisi++;
        }

        return $loker;
    }
}
