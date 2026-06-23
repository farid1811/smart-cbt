<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Group;
use App\Models\QuestionCode;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Question;
use App\Models\TryoutPackage;
use App\Models\LearningModule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Groups
        $groupSkd = Group::firstOrCreate(['name' => 'SKD']);
        $groupSnbt = Group::firstOrCreate(['name' => 'SNBT']);

        // 2. Create Admin & Peserta
        User::create([
            'name'     => 'Administrator',
            'username' => 'admin',
            'email'    => 'admin@smartcbt.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'no_hp'    => '081234567890',
            'is_active'=> true,
        ]);

        // Peserta SKD CPNS
        User::create([
            'name'       => 'Peserta SKD CPNS',
            'username'   => 'pesertaskd',
            'email'      => 'peserta@smartcbt.com',
            'password'   => Hash::make('password'),
            'role'       => 'peserta',
            'no_peserta' => 'P-001',
            'no_hp'      => '081234567891',
            'group_id'   => $groupSkd->id,
            'category'   => 'CPNS',
            'is_active'  => true,
        ]);

        // Peserta SNBT
        User::create([
            'name'       => 'Peserta SNBT',
            'username'   => 'pesertasnbt',
            'email'      => 'peserta2@smartcbt.com',
            'password'   => Hash::make('password'),
            'role'       => 'peserta',
            'no_peserta' => 'P-002',
            'no_hp'      => '081234567892',
            'group_id'   => $groupSnbt->id,
            'category'   => 'SNBT',
            'is_active'  => true,
        ]);

        // 3. Create Question Codes for SKD
        $codeTwk = QuestionCode::create(['group_id' => $groupSkd->id, 'name' => 'Tes Wawasan Kebangsaan', 'code' => 'TWK']);
        $codeTiu = QuestionCode::create(['group_id' => $groupSkd->id, 'name' => 'Tes Intelegensia Umum', 'code' => 'TIU']);
        $codeTkp = QuestionCode::create(['group_id' => $groupSkd->id, 'name' => 'Tes Karakteristik Pribadi', 'code' => 'TKP']);

        // Create Question Codes for SNBT
        $codePu = QuestionCode::create(['group_id' => $groupSnbt->id, 'name' => 'Penalaran Umum', 'code' => 'TPS-PU']);
        $codePpu = QuestionCode::create(['group_id' => $groupSnbt->id, 'name' => 'Pengetahuan & Pemahaman Umum', 'code' => 'TPS-PPU']);
        $codePk = QuestionCode::create(['group_id' => $groupSnbt->id, 'name' => 'Pengetahuan Kuantitatif', 'code' => 'TPS-PK']);
        $codePbm = QuestionCode::create(['group_id' => $groupSnbt->id, 'name' => 'Pemahaman Bacaan & Menulis', 'code' => 'TPS-PBM']);
        $codeLbi = QuestionCode::create(['group_id' => $groupSnbt->id, 'name' => 'Literasi Bahasa Indonesia', 'code' => 'LBI']);
        $codeLbing = QuestionCode::create(['group_id' => $groupSnbt->id, 'name' => 'Literasi Bahasa Inggris', 'code' => 'LBIng']);
        $codePm = QuestionCode::create(['group_id' => $groupSnbt->id, 'name' => 'Penalaran Matematika', 'code' => 'PM']);

        // 4. Create Categories & Sub Categories for TWK
        $catPilar = Category::create(['name' => 'Pilar Negara', 'question_code_id' => $codeTwk->id]);
        $subPancasila = SubCategory::create(['name' => 'Pancasila', 'category_id' => $catPilar->id]);
        $subUud = SubCategory::create(['name' => 'UUD 1945', 'category_id' => $catPilar->id]);
        $subNkri = SubCategory::create(['name' => 'NKRI', 'category_id' => $catPilar->id]);
        $subBhineka = SubCategory::create(['name' => 'Bhinneka Tunggal Ika', 'category_id' => $catPilar->id]);

        $catNasionalisme = Category::create(['name' => 'Nasionalisme', 'question_code_id' => $codeTwk->id]);
        $subSejarah = SubCategory::create(['name' => 'Sejarah Perjuangan', 'category_id' => $catNasionalisme->id]);
        $subBelaNegara = SubCategory::create(['name' => 'Bela Negara', 'category_id' => $catNasionalisme->id]);

        // Categories & Sub Categories for TIU
        $catVerbal = Category::create(['name' => 'Kemampuan Verbal', 'question_code_id' => $codeTiu->id]);
        $subSinonim = SubCategory::create(['name' => 'Sinonim', 'category_id' => $catVerbal->id]);
        $subAntonim = SubCategory::create(['name' => 'Antonim', 'category_id' => $catVerbal->id]);
        $subAnalogi = SubCategory::create(['name' => 'Analogi', 'category_id' => $catVerbal->id]);

        $catNumerik = Category::create(['name' => 'Kemampuan Numerik', 'question_code_id' => $codeTiu->id]);
        $subDeret = SubCategory::create(['name' => 'Deret Angka', 'category_id' => $catNumerik->id]);
        $subBerhitung = SubCategory::create(['name' => 'Berhitung Cepat', 'category_id' => $catNumerik->id]);
        $subSoalCerita = SubCategory::create(['name' => 'Soal Cerita', 'category_id' => $catNumerik->id]);

        // Categories & Sub Categories for TKP
        $catPublik = Category::create(['name' => 'Pelayanan Publik', 'question_code_id' => $codeTkp->id]);
        $subKeramahan = SubCategory::create(['name' => 'Keramahan', 'category_id' => $catPublik->id]);

        $catJejaring = Category::create(['name' => 'Jejaring Kerja', 'question_code_id' => $codeTkp->id]);
        $subKerjasama = SubCategory::create(['name' => 'Kerja Sama', 'category_id' => $catJejaring->id]);

        // Categories & Sub Categories for SNBT (TPS-PU & PM)
        $catInduktif = Category::create(['name' => 'Penalaran Induktif', 'question_code_id' => $codePu->id]);
        $subDeretHuruf = SubCategory::create(['name' => 'Deret Huruf', 'category_id' => $catInduktif->id]);

        $catDeduktif = Category::create(['name' => 'Penalaran Deduktif', 'question_code_id' => $codePu->id]);
        $subSilogisme = SubCategory::create(['name' => 'Silogisme', 'category_id' => $catDeduktif->id]);

        $catAljabar = Category::create(['name' => 'Aljabar dan Kalkulus', 'question_code_id' => $codePm->id]);
        $subPersamaan = SubCategory::create(['name' => 'Persamaan Linier', 'category_id' => $catAljabar->id]);

        // 5. Create Questions
        // TWK Questions
        $soalTwk = [];
        $soalTwkData = [
            ['soal' => 'Pancasila sebagai dasar negara Indonesia disahkan pada tanggal?', 'jawaban_benar' => 'B', 'opsi_a' => '17 Agustus 1945', 'opsi_b' => '18 Agustus 1945', 'opsi_c' => '1 Juni 1945', 'opsi_d' => '22 Juni 1945', 'sub_cat' => $subPancasila],
            ['soal' => 'Sila ke-3 Pancasila berbunyi?', 'jawaban_benar' => 'A', 'opsi_a' => 'Persatuan Indonesia', 'opsi_b' => 'Keadilan Sosial', 'opsi_c' => 'Kerakyatan yang Dipimpin', 'opsi_d' => 'Ketuhanan Yang Maha Esa', 'sub_cat' => $subPancasila],
            ['soal' => 'UUD 1945 terdiri dari berapa pasal setelah amandemen?', 'jawaban_benar' => 'C', 'opsi_a' => '37 pasal', 'opsi_b' => '49 pasal', 'opsi_c' => '73 pasal', 'opsi_d' => '60 pasal', 'sub_cat' => $subUud],
            ['soal' => 'Bhineka Tunggal Ika berasal dari kitab?', 'jawaban_benar' => 'B', 'opsi_a' => 'Negarakertagama', 'opsi_b' => 'Sutasoma', 'opsi_c' => 'Pararaton', 'opsi_d' => 'Serat Wedhatama', 'sub_cat' => $subBhineka],
            ['soal' => 'Sidang PPKI pertama dilaksanakan pada?', 'jawaban_benar' => 'A', 'opsi_a' => '18 Agustus 1945', 'opsi_b' => '19 Agustus 1945', 'opsi_c' => '22 Agustus 1945', 'opsi_d' => '29 Agustus 1945', 'sub_cat' => $subSejarah],
        ];
        foreach ($soalTwkData as $data) {
            $sub = $data['sub_cat'];
            $soalTwk[] = Question::create([
                'group_id'          => $groupSkd->id,
                'question_code_id'  => $codeTwk->id,
                'category_id'       => $sub->category_id,
                'sub_category_id'   => $sub->id,
                'soal'              => $data['soal'],
                'jawaban_benar'     => $data['jawaban_benar'],
                'opsi_a'            => $data['opsi_a'],
                'opsi_b'            => $data['opsi_b'],
                'opsi_c'            => $data['opsi_c'],
                'opsi_d'            => $data['opsi_d'],
                'opsi_e'            => null,
                'pembahasan'        => 'Ini adalah penjelasan atau pembahasan soal untuk ' . $data['soal'],
                'tingkat_kesulitan' => 'sedang',
            ]);
        }

        // TIU Questions
        $soalTiu = [];
        $soalTiuData = [
            ['soal' => 'Jika 5x + 10 = 35, maka nilai x adalah?', 'jawaban_benar' => 'B', 'opsi_a' => '4', 'opsi_b' => '5', 'opsi_c' => '6', 'opsi_d' => '7', 'sub_cat' => $subBerhitung],
            ['soal' => 'Deret: 2, 4, 8, 16, ... Angka berikutnya adalah?', 'jawaban_benar' => 'C', 'opsi_a' => '24', 'opsi_b' => '28', 'opsi_c' => '32', 'opsi_d' => '36', 'sub_cat' => $subDeret],
            ['soal' => 'Antonim dari kata "EKSPLISIT" adalah?', 'jawaban_benar' => 'A', 'opsi_a' => 'Implisit', 'opsi_b' => 'Jelas', 'opsi_c' => 'Terang', 'opsi_d' => 'Pasti', 'sub_cat' => $subAntonim],
            ['soal' => 'Sinonim dari kata "PREROGRATIF" adalah?', 'jawaban_benar' => 'D', 'opsi_a' => 'Kewajiban', 'opsi_b' => 'Tanggung jawab', 'opsi_c' => 'Sanksi', 'opsi_d' => 'Hak istimewa', 'sub_cat' => $subSinonim],
            ['soal' => 'Sebuah persegi memiliki sisi 8 cm. Luasnya adalah?', 'jawaban_benar' => 'B', 'opsi_a' => '48 cm²', 'opsi_b' => '64 cm²', 'opsi_c' => '32 cm²', 'opsi_d' => '56 cm²', 'sub_cat' => $subSoalCerita],
        ];
        foreach ($soalTiuData as $data) {
            $sub = $data['sub_cat'];
            $soalTiu[] = Question::create([
                'group_id'          => $groupSkd->id,
                'question_code_id'  => $codeTiu->id,
                'category_id'       => $sub->category_id,
                'sub_category_id'   => $sub->id,
                'soal'              => $data['soal'],
                'jawaban_benar'     => $data['jawaban_benar'],
                'opsi_a'            => $data['opsi_a'],
                'opsi_b'            => $data['opsi_b'],
                'opsi_c'            => $data['opsi_c'],
                'opsi_d'            => $data['opsi_d'],
                'opsi_e'            => null,
                'pembahasan'        => 'Pembahasan TIU: ' . $data['soal'],
                'tingkat_kesulitan' => 'sedang',
            ]);
        }

        // TKP Questions
        $soalTkp = [];
        $soalTkpData = [
            ['soal' => 'Saat rekan kerja Anda melakukan kesalahan yang berdampak pada tim, sikap Anda adalah?', 'jawaban_benar' => 'A', 'opsi_a' => 'Membantu memperbaiki dan memberi solusi bersama', 'opsi_b' => 'Melaporkan langsung ke atasan', 'opsi_c' => 'Diam saja karena bukan urusan Anda', 'opsi_d' => 'Memarahi rekan tersebut', 'sub_cat' => $subKeramahan],
            ['soal' => 'Ketika menghadapi pekerjaan dengan deadline ketat, Anda?', 'jawaban_benar' => 'B', 'opsi_a' => 'Panik dan meminta bantuan semua orang', 'opsi_b' => 'Membuat prioritas dan mengerjakan secara sistematis', 'opsi_c' => 'Meminta perpanjangan waktu', 'opsi_d' => 'Mengerjakan seadanya', 'sub_cat' => $subKerjasama],
            ['soal' => 'Anda diberi tugas yang tidak sesuai keahlian Anda. Anda akan?', 'jawaban_benar' => 'A', 'opsi_a' => 'Mempelajari dan berusaha menyelesaikan dengan belajar', 'opsi_b' => 'Menolak tugas tersebut', 'opsi_c' => 'Menyerahkan ke orang lain', 'opsi_d' => 'Mengerjakan asal jadi', 'sub_cat' => $subKerjasama],
            ['soal' => 'Saat ada konflik antar tim, peran Anda sebaiknya?', 'jawaban_benar' => 'C', 'opsi_a' => 'Memihak salah satu pihak yang benar', 'opsi_b' => 'Menghindari konflik tersebut', 'opsi_c' => 'Menjadi mediator yang netral dan mencari solusi', 'opsi_d' => 'Melaporkan ke HR', 'sub_cat' => $subKeramahan],
            ['soal' => 'Bagaimana sikap Anda terhadap kritik dari atasan?', 'jawaban_benar' => 'B', 'opsi_a' => 'Merasa tersinggung dan membela diri', 'opsi_b' => 'Menerima dengan terbuka dan menjadikan bahan evaluasi', 'opsi_c' => 'Diam saja tanpa respons', 'opsi_d' => 'Membalas kritik dengan kritik', 'sub_cat' => $subKerjasama],
        ];
        foreach ($soalTkpData as $data) {
            $sub = $data['sub_cat'];
            $soalTkp[] = Question::create([
                'group_id'          => $groupSkd->id,
                'question_code_id'  => $codeTkp->id,
                'category_id'       => $sub->category_id,
                'sub_category_id'   => $sub->id,
                'soal'              => $data['soal'],
                'jawaban_benar'     => $data['jawaban_benar'],
                'opsi_a'            => $data['opsi_a'],
                'opsi_b'            => $data['opsi_b'],
                'opsi_c'            => $data['opsi_c'],
                'opsi_d'            => $data['opsi_d'],
                'opsi_e'            => null,
                'pembahasan'        => 'Pembahasan TKP: ' . $data['soal'],
                'tingkat_kesulitan' => 'sedang',
            ]);
        }

        // SNBT Questions (TPS-PU & PM)
        $soalTps = [];
        $soalTpsData = [
            ['soal' => 'Jika "KUCING" ditulis sebagai "LVDJOH", maka "ANJING" ditulis sebagai?', 'jawaban_benar' => 'C', 'opsi_a' => 'BOJKOH', 'opsi_b' => 'BPKLOI', 'opsi_c' => 'BOKKPI', 'opsi_d' => 'BOKJOH', 'sub_cat' => $subDeretHuruf],
            ['soal' => 'Semua mahasiswa rajin belajar. Sebagian orang rajin belajar adalah atlet. Kesimpulan yang tepat adalah?', 'jawaban_benar' => 'A', 'opsi_a' => 'Sebagian mahasiswa rajin belajar mungkin atlet', 'opsi_b' => 'Semua atlet adalah mahasiswa', 'opsi_c' => 'Tidak ada mahasiswa yang atlet', 'opsi_d' => 'Semua mahasiswa adalah atlet', 'sub_cat' => $subSilogisme],
        ];
        foreach ($soalTpsData as $data) {
            $sub = $data['sub_cat'];
            $soalTps[] = Question::create([
                'group_id'          => $groupSnbt->id,
                'question_code_id'  => $codePu->id,
                'category_id'       => $sub->category_id,
                'sub_category_id'   => $sub->id,
                'soal'              => $data['soal'],
                'jawaban_benar'     => $data['jawaban_benar'],
                'opsi_a'            => $data['opsi_a'],
                'opsi_b'            => $data['opsi_b'],
                'opsi_c'            => $data['opsi_c'],
                'opsi_d'            => $data['opsi_d'],
                'opsi_e'            => null,
                'pembahasan'        => 'Pembahasan TPS: ' . $data['soal'],
                'tingkat_kesulitan' => 'sedang',
            ]);
        }

        $soalPmat = [];
        $soalPmatData = [
            ['soal' => 'Rata-rata tinggi 5 orang siswa adalah 160 cm. Jika ditambah seorang siswa baru, rata-ratanya menjadi 161 cm. Tinggi siswa baru tersebut adalah?', 'jawaban_benar' => 'D', 'opsi_a' => '162 cm', 'opsi_b' => '164 cm', 'opsi_c' => '165 cm', 'opsi_d' => '166 cm', 'sub_cat' => $subPersamaan],
            ['soal' => 'Harga tiket bioskop untuk 3 orang dewasa dan 2 anak-anak adalah Rp 190.000. Jika harga tiket dewasa Rp 10.000 lebih mahal dari tiket anak-anak, berapakah harga tiket dewasa?', 'jawaban_benar' => 'B', 'opsi_a' => 'Rp 35.000', 'opsi_b' => 'Rp 42.000', 'opsi_c' => 'Rp 45.000', 'opsi_d' => 'Rp 50.000', 'sub_cat' => $subPersamaan],
        ];
        foreach ($soalPmatData as $data) {
            $sub = $data['sub_cat'];
            $soalPmat[] = Question::create([
                'group_id'          => $groupSnbt->id,
                'question_code_id'  => $codePm->id,
                'category_id'       => $sub->category_id,
                'sub_category_id'   => $sub->id,
                'soal'              => $data['soal'],
                'jawaban_benar'     => $data['jawaban_benar'],
                'opsi_a'            => $data['opsi_a'],
                'opsi_b'            => $data['opsi_b'],
                'opsi_c'            => $data['opsi_c'],
                'opsi_d'            => $data['opsi_d'],
                'opsi_e'            => null,
                'pembahasan'        => 'Pembahasan Penalaran Matematika: ' . $data['soal'],
                'tingkat_kesulitan' => 'sedang',
            ]);
        }

        // 6. Create Packages
        $paketSkd = TryoutPackage::create([
            'nama'         => 'Tryout SKD CPNS #1',
            'deskripsi'    => 'Simulasi CAT lengkap Seleksi Kompetensi Dasar CPNS (TWK, TIU, TKP).',
            'jenis_ujian'  => 'tryout',
            'group'        => 'SKD',
            'category'     => 'CPNS',
            'durasi_menit' => 90,
            'is_active'    => true,
            'attempt_limit'=> 2,
        ]);
        $urutan = 1;
        foreach ($soalTwk as $soal) { $soal->update(['tryout_package_id' => $paketSkd->id, 'urutan' => $urutan++]); }
        foreach ($soalTiu as $soal) { $soal->update(['tryout_package_id' => $paketSkd->id, 'urutan' => $urutan++]); }
        foreach ($soalTkp as $soal) { $soal->update(['tryout_package_id' => $paketSkd->id, 'urutan' => $urutan++]); }

        $paketSnbt = TryoutPackage::create([
            'nama'         => 'Drill Soal Kognitif SNBT',
            'deskripsi'    => 'Latihan intensif (Drill) khusus materi TPS dan Penalaran Matematika.',
            'jenis_ujian'  => 'drill',
            'group'        => 'SNBT',
            'category'     => 'SNBT',
            'durasi_menit' => 30,
            'is_active'    => true,
            'attempt_limit'=> 2,
        ]);
        $urutan = 1;
        foreach ($soalTps as $soal) { $soal->update(['tryout_package_id' => $paketSnbt->id, 'urutan' => $urutan++]); }
        foreach ($soalPmat as $soal) { $soal->update(['tryout_package_id' => $paketSnbt->id, 'urutan' => $urutan++]); }

        // 7. Seed Learning Modules
        LearningModule::create([
            'group_id'          => $groupSkd->id,
            'question_code_id'  => $codeTwk->id,
            'category_id'       => $catPilar->id,
            'sub_category_id'   => $subPancasila->id,
            'name'              => 'Modul Pancasila dan Pilar Negara',
            'description'       => 'Materi pembelajaran mendalam mengenai pengamalan sila-sila Pancasila dalam kehidupan sehari-hari.',
            'pdf_file'          => null,
            'video_url'         => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_active'         => true,
        ]);

        LearningModule::create([
            'group_id'          => $groupSnbt->id,
            'question_code_id'  => $codePm->id,
            'category_id'       => $catAljabar->id,
            'sub_category_id'   => $subPersamaan->id,
            'name'              => 'Modul Persamaan Linier Dua Variabel',
            'description'       => 'Materi ringkas dan trik cepat menyelesaikan soal SPLDV untuk Penalaran Matematika SNBT.',
            'pdf_file'          => null,
            'video_url'         => null,
            'is_active'         => true,
        ]);
    }
}

