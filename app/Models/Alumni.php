<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    protected $table = 'alumni';

    protected $fillable = [
        'nama',
        'instansi',
        'tahun_lulus',
        'foto',
        'urutan',
    ];

    protected $casts = [
        'tahun_lulus' => 'integer',
        'urutan'      => 'integer',
    ];

    /**
     * Get the URL of the alumni photo.
     */
    public function getFotoUrlAttribute(): string
    {
        if ($this->foto && file_exists(public_path('storage/' . $this->foto))) {
            return asset('storage/' . $this->foto);
        }
        // Return a default placeholder avatar
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama) . '&size=400&background=EFF6FF&color=2563EB&bold=true';
    }
}
