<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Style\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;

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
        'two_factor_secret',
        'two_factor_confirmed_at',
    ];

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

    public function pengajuanLibur()
    {
        return $this->hasMany(PengajuanLibur::class, 'id_user', 'id_user');
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

        $google2fa = new Google2FA();
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $this->email,
            decrypt($this->two_factor_secret)
        );

        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            )
        );

        return $writer->writeString($qrCodeUrl);
    }
}
