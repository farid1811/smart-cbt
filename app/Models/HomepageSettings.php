<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSettings extends Model
{
    protected $table = 'homepage_settings';

    protected $fillable = [
        'hero_badge',
        'hero_title',
        'hero_subtitle',
        'hero_cta_primary',
        'hero_cta_whatsapp',
        'hero_passing_rate',

        'nama_lembaga',
        'tagline',
        'whatsapp_number',
        'email',
        'instagram',
        'alamat',

        'program_section_title',
        'program_section_subtitle',
        'alumni_section_title',
        'alumni_section_subtitle',
        'testimoni_section_title',
        'testimoni_section_subtitle',
        'faq_section_title',
        'faq_section_subtitle',

        'footer_description',
        'meta_title',
        'meta_description',
    ];

    /**
     * Get the singleton settings record, creating it if it doesn't exist.
     */
    public static function getInstance(): self
    {
        return self::firstOrCreate(['id' => 1]);
    }
}
