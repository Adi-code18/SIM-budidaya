<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Services\Google2FA;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; 

    protected $table = 'users';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'no_tlp',
        'foto_profil',
        'two_factor_secret',
        'two_factor_confirmed_at',
        'last_session_id',
    ];

    protected $appends = [
        'foto_profil_url',
    ];

    public function getFotoProfilUrlAttribute(): string
    {
        if ($this->foto_profil && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->foto_profil)) {
            return asset('storage/' . $this->foto_profil);
        }

        $name = urlencode($this->nama ?: 'User');
        return "https://ui-avatars.com/api/?name={$name}&background=0B2570&color=ffffff&bold=true";
    }

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function hasTwoFactorEnabled(): bool
    {
        return !is_null($this->two_factor_confirmed_at);
    }

    public function kolam()
    {
        return $this->hasMany(Kolam::class, 'id_user', 'id_user');
    }

    public function manajemenPakan()
    {
        return $this->hasMany(ManajemenPakan::class, 'id_user', 'id_user');
    }

    public function batchPembibitan()
    {
        return $this->hasMany(BatchPembibitan::class, 'id_user', 'id_user');
    }

    public function batchPembesaran()
    {
        return $this->hasMany(BatchPembesaran::class, 'id_user', 'id_user');
    }

    public function keuangan()
    {
        return $this->hasMany(Keuangan::class, 'id_user', 'id_user');
    }

    public function mitraDistributor()
    {
        return $this->hasMany(MitraDistributor::class, 'id_user', 'id_user');
    }

    public function transaksiDistribusi()
    {
        return $this->hasMany(TransaksiDistribusi::class, 'id_user', 'id_user');
    }

    public function getTwoFactorQrCodeSvgAttribute(): string
    {
        if (!$this->two_factor_secret) {
            return '';
        }

        try {
            $secret = decrypt($this->two_factor_secret);
        } catch (\Throwable $e) {
            $secret = (string) $this->two_factor_secret;
        }

        $google2fa = new Google2FA();
        $accountName = $this->email ?: ($this->no_tlp ?: ($this->nama ?: 'petugas@sim-budidaya.id'));
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name', 'SIM-BUDIDAYA'),
            $accountName,
            $secret
        );

        if (class_exists(\BaconQrCode\Writer::class)) {
            try {
                $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                    new \BaconQrCode\Renderer\RendererStyle\RendererStyle(190),
                    new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                );
                $writer = new \BaconQrCode\Writer($renderer);
                return $writer->writeString($qrCodeUrl);
            } catch (\Throwable $e) {
                // Fallback to inline HTML QR code
            }
        }

        return $google2fa->getQRCodeInlineHtml($qrCodeUrl, 190);
    }
}
