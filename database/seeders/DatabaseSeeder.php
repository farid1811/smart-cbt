<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\Category;
use App\Models\Question;
use App\Models\TryoutPackage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Get or Create groups
        $groupSkd = Group::firstOrCreate(['name' => 'SKD']);
        $groupSnbt = Group::firstOrCreate(['name' => 'SNBT']);

        // Admin
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@smartcbt.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Peserta dummy SKD
        User::create([
            'name'       => 'Peserta SKD CPNS',
            'email'      => 'peserta@smartcbt.com',
            'password'   => Hash::make('password'),
            'role'       => 'peserta',
            'no_peserta' => 'P-001',
            'group_id'   => $groupSkd->id,
            'is_active'  => true,
        ]);

        // Peserta dummy SNBT
        User::create([
            'name'       => 'Peserta SNBT',
            'email'      => 'peserta2@smartcbt.com',
            'password'   => Hash::make('password'),
            'role'       => 'peserta',
            'no_peserta' => 'P-002',
            'group_id'   => $groupSnbt->id,
            'is_active'  => true,
        ]);

        // Kategori SKD
        $twk = Category::create([
            'name' => 'Tes Wawasan Kebangsaan',
            'kode' => 'TWK',
            'deskripsi' => 'Soal seputar kebangsaan, UUD 1945, Pancasila, NKRI, dan Bhinneka Tunggal Ika.',
            'group_id' => $groupSkd->id
        ]);
        $tiu = Category::create([
            'name' => 'Tes Intelegensia Umum',
            'kode' => 'TIU',
            'deskripsi' => 'Soal kemampuan verbal, numerik, dan figural.',
            'group_id' => $groupSkd->id
        ]);
        $tkp = Category::create([
            'name' => 'Tes Karakteristik Pribadi',
            'kode' => 'TKP',
            'deskripsi' => 'Soal karakteristik dan perilaku peserta dalam situasi kerja.',
            'group_id' => $groupSkd->id
        ]);
        $kdn = Category::create([
            'name' => 'Kedinasan',
            'kode' => 'KDN',
            'deskripsi' => 'Materi khusus seleksi sekolah kedinasan.',
            'group_id' => $groupSkd->id
        ]);
        $cpn = Category::create([
            'name' => 'CPNS',
            'kode' => 'CPN',
            'deskripsi' => 'Materi seleksi calon pegawai negeri sipil.',
            'group_id' => $groupSkd->id
        ]);

        // Kategori SNBT
        $tps = Category::create([
            'name' => 'TPS',
            'kode' => 'TPS',
            'deskripsi' => 'Tes Potensi Skolastik mengukur kemampuan kognitif dan penalaran.',
            'group_id' => $groupSnbt->id
        ]);
        $lInd = Category::create([
            'name' => 'Literasi Indonesia',
            'kode' => 'LIND',
            'deskripsi' => 'Menguji pemahaman teks dan analisis bahasa Indonesia.',
            'group_id' => $groupSnbt->id
        ]);
        $lIng = Category::create([
            'name' => 'Literasi Inggris',
            'kode' => 'LING',
            'deskripsi' => 'Menguji pemahaman teks dan kosa kata bahasa Inggris.',
            'group_id' => $groupSnbt->id
        ]);
        $pMat = Category::create([
            'name' => 'Penalaran Matematika',
            'kode' => 'PMAT',
            'deskripsi' => 'Pemecahan masalah matematika dalam berbagai konteks kehidupan nyata.',
            'group_id' => $groupSnbt->id
        ]);

        // Soal dummy TWK
        $soalTwk = [];
        $soalTwkData = [
            ['soal' => 'Pancasila sebagai dasar negara Indonesia disahkan pada tanggal?', 'jawaban_benar' => 'B', 'opsi_a' => '17 Agustus 1945', 'opsi_b' => '18 Agustus 1945', 'opsi_c' => '1 Juni 1945', 'opsi_d' => '22 Juni 1945'],
            ['soal' => 'Sila ke-3 Pancasila berbunyi?', 'jawaban_benar' => 'A', 'opsi_a' => 'Persatuan Indonesia', 'opsi_b' => 'Keadilan Sosial', 'opsi_c' => 'Kerakyatan yang Dipimpin', 'opsi_d' => 'Ketuhanan Yang Maha Esa'],
            ['soal' => 'UUD 1945 terdiri dari berapa pasal setelah amandemen?', 'jawaban_benar' => 'C', 'opsi_a' => '37 pasal', 'opsi_b' => '49 pasal', 'opsi_c' => '73 pasal', 'opsi_d' => '60 pasal'],
            ['soal' => 'Bhineka Tunggal Ika berasal dari kitab?', 'jawaban_benar' => 'B', 'opsi_a' => 'Negarakertagama', 'opsi_b' => 'Sutasoma', 'opsi_c' => 'Pararaton', 'opsi_d' => 'Serat Wedhatama'],
            ['soal' => 'Sidang PPKI pertama dilaksanakan pada?', 'jawaban_benar' => 'A', 'opsi_a' => '18 Agustus 1945', 'opsi_b' => '19 Agustus 1945', 'opsi_c' => '22 Agustus 1945', 'opsi_d' => '29 Agustus 1945'],
        ];
        foreach ($soalTwkData as $data) {
            $soalTwk[] = Question::create(array_merge($data, ['category_id' => $twk->id, 'opsi_e' => null, 'tingkat_kesulitan' => 'sedang']));
        }

        // Soal dummy TIU
        $soalTiu = [];
        $soalTiuData = [
            ['soal' => 'Jika 5x + 10 = 35, maka nilai x adalah?', 'jawaban_benar' => 'B', 'opsi_a' => '4', 'opsi_b' => '5', 'opsi_c' => '6', 'opsi_d' => '7'],
            ['soal' => 'Deret: 2, 4, 8, 16, ... Angka berikutnya adalah?', 'jawaban_benar' => 'C', 'opsi_a' => '24', 'opsi_b' => '28', 'opsi_c' => '32', 'opsi_d' => '36'],
            ['soal' => 'Antonim dari kata "EKSPLISIT" adalah?', 'jawaban_benar' => 'A', 'opsi_a' => 'Implisit', 'opsi_b' => 'Jelas', 'opsi_c' => 'Terang', 'opsi_d' => 'Pasti'],
            ['soal' => 'Sinonim dari kata "PREROGRATIF" adalah?', 'jawaban_benar' => 'D', 'opsi_a' => 'Kewajiban', 'opsi_b' => 'Tanggung jawab', 'opsi_c' => 'Sanksi', 'opsi_d' => 'Hak istimewa'],
            ['soal' => 'Sebuah persegi memiliki sisi 8 cm. Luasnya adalah?', 'jawaban_benar' => 'B', 'opsi_a' => '48 cm²', 'opsi_b' => '64 cm²', 'opsi_c' => '32 cm²', 'opsi_d' => '56 cm²'],
        ];
        foreach ($soalTiuData as $data) {
            $soalTiu[] = Question::create(array_merge($data, ['category_id' => $tiu->id, 'opsi_e' => null, 'tingkat_kesulitan' => 'sedang']));
        }

        // Soal dummy TKP
        $soalTkp = [];
        $soalTkpData = [
            ['soal' => 'Saat rekan kerja Anda melakukan kesalahan yang berdampak pada tim, sikap Anda adalah?', 'jawaban_benar' => 'A', 'opsi_a' => 'Membantu memperbaiki dan memberi solusi bersama', 'opsi_b' => 'Melaporkan langsung ke atasan', 'opsi_c' => 'Diam saja karena bukan urusan Anda', 'opsi_d' => 'Memarahi rekan tersebut'],
            ['soal' => 'Ketika menghadapi pekerjaan dengan deadline ketat, Anda?', 'jawaban_benar' => 'B', 'opsi_a' => 'Panik dan meminta bantuan semua orang', 'opsi_b' => 'Membuat prioritas dan mengerjakan secara sistematis', 'opsi_c' => 'Meminta perpanjangan waktu', 'opsi_d' => 'Mengerjakan seadanya'],
            ['soal' => 'Anda diberi tugas yang tidak sesuai keahlian Anda. Anda akan?', 'jawaban_benar' => 'A', 'opsi_a' => 'Mempelajari dan berusaha menyelesaikan dengan belajar', 'opsi_b' => 'Menolak tugas tersebut', 'opsi_c' => 'Menyerahkan ke orang lain', 'opsi_d' => 'Mengerjakan asal jadi'],
            ['soal' => 'Saat ada konflik antar tim, peran Anda sebaiknya?', 'jawaban_benar' => 'C', 'opsi_a' => 'Memihak salah satu pihak yang benar', 'opsi_b' => 'Menghindari konflik tersebut', 'opsi_c' => 'Menjadi mediator yang netral dan mencari solusi', 'opsi_d' => 'Melaporkan ke HR'],
            ['soal' => 'Bagaimana sikap Anda terhadap kritik dari atasan?', 'jawaban_benar' => 'B', 'opsi_a' => 'Merasa tersinggung dan membela diri', 'opsi_b' => 'Menerima dengan terbuka dan menjadikan bahan evaluasi', 'opsi_c' => 'Diam saja tanpa respons', 'opsi_d' => 'Membalas kritik dengan kritik'],
        ];
        foreach ($soalTkpData as $data) {
            $soalTkp[] = Question::create(array_merge($data, ['category_id' => $tkp->id, 'opsi_e' => null, 'tingkat_kesulitan' => 'sedang']));
        }

        // Soal dummy TPS (SNBT)
        $soalTps = [];
        $soalTpsData = [
            ['soal' => 'Jika "KUCING" ditulis sebagai "LVDJOH", maka "ANJING" ditulis sebagai?', 'jawaban_benar' => 'C', 'opsi_a' => 'BOJKOH', 'opsi_b' => 'BPKLOI', 'opsi_c' => 'BOKKPI', 'opsi_d' => 'BOKJOH'],
            ['soal' => 'Semua mahasiswa rajin belajar. Sebagian orang rajin belajar adalah atlet. Kesimpulan yang tepat adalah?', 'jawaban_benar' => 'A', 'opsi_a' => 'Sebagian mahasiswa rajin belajar mungkin atlet', 'opsi_b' => 'Semua atlet adalah mahasiswa', 'opsi_c' => 'Tidak ada mahasiswa yang atlet', 'opsi_d' => 'Semua mahasiswa adalah atlet'],
        ];
        foreach ($soalTpsData as $data) {
            $soalTps[] = Question::create(array_merge($data, ['category_id' => $tps->id, 'opsi_e' => null, 'tingkat_kesulitan' => 'sedang']));
        }

        // Soal dummy Penalaran Matematika (SNBT)
        $soalPmat = [];
        $soalPmatData = [
            ['soal' => 'Rata-rata tinggi 5 orang siswa adalah 160 cm. Jika ditambah seorang siswa baru, rata-ratanya menjadi 161 cm. Tinggi siswa baru tersebut adalah?', 'jawaban_benar' => 'D', 'opsi_a' => '162 cm', 'opsi_b' => '164 cm', 'opsi_c' => '165 cm', 'opsi_d' => '166 cm'],
            ['soal' => 'Harga tiket bioskop untuk 3 orang dewasa dan 2 anak-anak adalah Rp 190.000. Jika harga tiket dewasa Rp 10.000 lebih mahal dari tiket anak-anak, berapakah harga tiket dewasa?', 'jawaban_benar' => 'B', 'opsi_a' => 'Rp 35.000', 'opsi_b' => 'Rp 42.000', 'opsi_c' => 'Rp 45.000', 'opsi_d' => 'Rp 50.000'],
        ];
        foreach ($soalPmatData as $data) {
            $soalPmat[] = Question::create(array_merge($data, ['category_id' => $pMat->id, 'opsi_e' => null, 'tingkat_kesulitan' => 'sedang']));
        }

        // Paket Ujian 1: Tryout SKD CPNS #1
        $paketSkd = TryoutPackage::create([
            'nama'         => 'Tryout SKD CPNS #1',
            'deskripsi'    => 'Simulasi CAT lengkap Seleksi Kompetensi Dasar CPNS (TWK, TIU, TKP).',
            'jenis_ujian'  => 'tryout',
            'durasi_menit' => 90,
            'is_active'    => true,
        ]);
        $urutan = 1;
        foreach ($soalTwk as $soal) { $paketSkd->questions()->attach($soal->id, ['urutan' => $urutan++]); }
        foreach ($soalTiu as $soal) { $paketSkd->questions()->attach($soal->id, ['urutan' => $urutan++]); }
        foreach ($soalTkp as $soal) { $paketSkd->questions()->attach($soal->id, ['urutan' => $urutan++]); }

        // Paket Ujian 2: Drill Soal SNBT TPS & Matematika
        $paketSnbt = TryoutPackage::create([
            'nama'         => 'Drill Soal Kognitif SNBT',
            'deskripsi'    => 'Latihan intensif (Drill) khusus materi TPS dan Penalaran Matematika.',
            'jenis_ujian'  => 'drill',
            'durasi_menit' => 30,
            'is_active'    => true,
        ]);
        $urutan = 1;
        foreach ($soalTps as $soal) { $paketSnbt->questions()->attach($soal->id, ['urutan' => $urutan++]); }
        foreach ($soalPmat as $soal) { $paketSnbt->questions()->attach($soal->id, ['urutan' => $urutan++]); }
    }
}
