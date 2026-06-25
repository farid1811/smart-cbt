<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSettings;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    /**
     * Show the CMS homepage settings form.
     */
    public function index()
    {
        $settings = HomepageSettings::getInstance();
        return view('admin.homepage.index', compact('settings'));
    }

    /**
     * Save/update the homepage settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Hero
            'hero_badge'          => 'required|string|max:200',
            'hero_title'          => 'required|string|max:500',
            'hero_subtitle'       => 'required|string|max:1000',
            'hero_cta_primary'    => 'required|string|max:100',
            'hero_cta_whatsapp'   => 'required|string|max:100',
            'hero_passing_rate'   => 'required|numeric|min:0|max:100',

            // Identity
            'nama_lembaga'        => 'required|string|max:200',
            'tagline'             => 'required|string|max:200',
            'whatsapp_number'     => 'required|string|max:30',
            'email'               => 'required|email|max:200',
            'instagram'           => 'required|string|max:100',
            'alamat'              => 'required|string|max:500',

            // Section Headings
            'program_section_title'      => 'required|string|max:300',
            'program_section_subtitle'   => 'required|string|max:1000',
            'alumni_section_title'       => 'required|string|max:300',
            'alumni_section_subtitle'    => 'required|string|max:1000',
            'testimoni_section_title'    => 'required|string|max:300',
            'testimoni_section_subtitle' => 'required|string|max:1000',
            'faq_section_title'          => 'required|string|max:300',
            'faq_section_subtitle'       => 'required|string|max:1000',

            // Footer & Meta
            'footer_description'  => 'required|string|max:1000',
            'meta_title'          => 'required|string|max:200',
            'meta_description'    => 'required|string|max:500',
        ]);

        $settings = HomepageSettings::getInstance();
        $settings->update($validated);

        return back()->with('success', 'Pengaturan Homepage berhasil disimpan.');
    }
}
