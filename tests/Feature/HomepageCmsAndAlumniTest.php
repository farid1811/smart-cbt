<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Alumni;
use App\Models\HomepageSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomepageCmsAndAlumniTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $peserta;

    protected function setUp(): void
    {
        parent::setUp();

        // Create an admin user
        $this->admin = User::create([
            'name' => 'Admin User',
            'username' => 'admin_test',
            'email' => 'admin_test@smartcbt.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Create a regular participant user
        $this->peserta = User::create([
            'name' => 'Peserta User',
            'username' => 'peserta_test',
            'email' => 'peserta_test@smartcbt.com',
            'password' => bcrypt('password'),
            'role' => 'peserta',
            'is_active' => true,
        ]);
    }

    /**
     * Test that the landing page loads successfully and retrieves settings.
     */
    public function test_landing_page_loads_with_homepage_settings(): void
    {
        // Fetch instance and make sure it exists
        $settings = HomepageSettings::getInstance();
        $settings->update([
            'nama_lembaga' => 'Lembaga Pendidikan Test',
            'tagline' => 'Tagline Pendidikan Keren',
        ]);

        $response = $this->get(route('landing'));

        $response->assertStatus(200);
        $response->assertSee('Lembaga Pendidikan Test');
        $response->assertSee('Tagline Pendidikan Keren');
    }

    /**
     * Test that the homepage settings page is protected by admin middleware.
     */
    public function test_homepage_settings_requires_admin_role(): void
    {
        // Guest gets redirected to login
        $response = $this->get(route('admin.homepage.index'));
        $response->assertRedirect(route('login'));

        // Peserta gets 403 Forbidden
        $response = $this->actingAs($this->peserta)->get(route('admin.homepage.index'));
        $response->assertStatus(403);
    }

    /**
     * Test that the homepage settings index page loads successfully for admins.
     */
    public function test_admin_can_view_homepage_settings(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.homepage.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Homepage');
        // Check that SEO preview does NOT show domain URL
        $response->assertSee('Preview tampilan di Google');
        $response->assertDontSee('https://127.0.0.1:8000');
    }

    /**
     * Test that the admin can update the homepage settings.
     */
    public function test_admin_can_update_homepage_settings(): void
    {
        $payload = [
            'nama_lembaga' => 'CBT Premium Edu',
            'tagline' => 'Solusi Sukses Ujian',
            'whatsapp_number' => '08999999999',
            'email' => 'info@premiumedu.com',
            'instagram' => 'premium.edu',
            'alamat' => 'Jalan Pendidikan No. 10',
            'hero_badge' => 'Paling Populer',
            'hero_title' => 'Lolos Ujian Impian Anda',
            'hero_subtitle' => 'Dilengkapi ribuan bank soal terupdate',
            'hero_cta_primary' => 'Mulai Belajar Sekarang',
            'hero_cta_whatsapp' => 'Hubungi Kami',
            'hero_passing_rate' => 98,
            'program_section_title' => 'Program Unggulan Kami',
            'program_section_subtitle' => 'Materi berkualitas tinggi untuk hasil optimal',
            'alumni_section_title' => 'Apa Kata Mereka?',
            'alumni_section_subtitle' => 'Testimoni kelulusan alumni kami',
            'testimoni_section_title' => 'Ulasan Pengguna',
            'testimoni_section_subtitle' => 'Rating and tanggapan dari siswa',
            'faq_section_title' => 'Pertanyaan Umum',
            'faq_section_subtitle' => 'Segala hal yang perlu Anda ketahui',
            'footer_description' => 'Platform CBT terbaik nomor 1',
            'meta_title' => 'CBT Premium - Lulus Ujian CPNS & SNBT',
            'meta_description' => 'Aplikasi simulasi CAT CPNS & SNBT modern dengan sistem realtime.',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.homepage.update'), $payload);

        $response->assertRedirect(); // Redirects back
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('homepage_settings', [
            'nama_lembaga' => 'CBT Premium Edu',
            'hero_passing_rate' => 98,
        ]);
    }

    /**
     * Test that the alumni CRUD index loads successfully.
     */
    public function test_admin_can_view_alumni_index(): void
    {
        Alumni::create([
            'nama' => 'Alumni Hebat One',
            'instansi' => 'STAN',
            'tahun_lulus' => 2025,
            'urutan' => 1,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.alumni.index'));

        $response->assertStatus(200);
        $response->assertSee('Daftar Alumni');
        $response->assertSee('Alumni Hebat One');
    }

    /**
     * Test that the admin can store a new alumni.
     */
    public function test_admin_can_store_alumni(): void
    {
        Storage::fake('public');
        // Use create() instead of image() to avoid GD extension dependency
        $fakePhoto = UploadedFile::fake()->create('alumni_photo.jpg', 100, 'image/jpeg');

        $payload = [
            'nama' => 'Budi Sudarsono',
            'instansi' => 'IPDN',
            'tahun_lulus' => 2026,
            'urutan' => 5,
            'foto' => $fakePhoto,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.alumni.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('alumni', [
            'nama' => 'Budi Sudarsono',
            'instansi' => 'IPDN',
            'tahun_lulus' => 2026,
            'urutan' => 5,
        ]);

        $alumnus = Alumni::where('nama', 'Budi Sudarsono')->first();
        $this->assertNotNull($alumnus->foto);
        Storage::disk('public')->assertExists($alumnus->foto);
    }

    /**
     * Test that the admin can view the alumni edit form.
     */
    public function test_admin_can_view_alumni_edit_page(): void
    {
        $alumnus = Alumni::create([
            'nama' => 'Chandra Wijaya',
            'instansi' => 'UI',
            'tahun_lulus' => 2024,
            'urutan' => 2,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.alumni.edit', $alumnus));

        $response->assertStatus(200);
        $response->assertSee('Edit Alumni');
        $response->assertSee('Chandra Wijaya');
    }

    /**
     * Test that the admin can update an alumni.
     */
    public function test_admin_can_update_alumni(): void
    {
        Storage::fake('public');
        // Use create() instead of image() to avoid GD extension dependency
        $initialPhoto = UploadedFile::fake()->create('old_photo.jpg', 100, 'image/jpeg');

        $alumnus = Alumni::create([
            'nama' => 'Dewi Persik',
            'instansi' => 'ITB',
            'tahun_lulus' => 2023,
            'urutan' => 3,
            'foto' => $initialPhoto->store('alumni', 'public'),
        ]);

        $newPhoto = UploadedFile::fake()->create('new_photo.jpg', 100, 'image/jpeg');

        $payload = [
            'nama' => 'Dewi Persik updated',
            'instansi' => 'UGM',
            'tahun_lulus' => 2024,
            'urutan' => 10,
            'foto' => $newPhoto,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.alumni.update', $alumnus), $payload);

        $response->assertRedirect(route('admin.alumni.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('alumni', [
            'id' => $alumnus->id,
            'nama' => 'Dewi Persik updated',
            'instansi' => 'UGM',
            'tahun_lulus' => 2024,
            'urutan' => 10,
        ]);

        $alumnus->refresh();
        Storage::disk('public')->assertExists($alumnus->foto);
    }

    /**
     * Test that the admin can delete an alumni.
     */
    public function test_admin_can_delete_alumni(): void
    {
        Storage::fake('public');
        // Use create() instead of image() to avoid GD extension dependency
        $photo = UploadedFile::fake()->create('alumni.jpg', 100, 'image/jpeg');
        $photoPath = $photo->store('alumni', 'public');

        $alumnus = Alumni::create([
            'nama' => 'Eko Prasetyo',
            'instansi' => 'ITS',
            'tahun_lulus' => 2022,
            'urutan' => 12,
            'foto' => $photoPath,
        ]);

        Storage::disk('public')->assertExists($photoPath);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.alumni.destroy', $alumnus));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('alumni', [
            'id' => $alumnus->id,
        ]);
        Storage::disk('public')->assertMissing($photoPath);
    }
}
