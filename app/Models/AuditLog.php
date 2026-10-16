<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'id_user',
        'nama_user',
        'role',
        'action',
        'kategori',
        'deskripsi',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Catat aktivitas penting sistem secara cepat dan seragam.
     */
    public static function record(string $action, string $kategori, string $deskripsi, ?User $user = null): self
    {
        $currentUser = $user ?? Auth::user();

        return self::create([
            'id_user'    => $currentUser ? $currentUser->id_user : null,
            'nama_user'  => $currentUser ? ($currentUser->nama ?? $currentUser->name ?? 'Sistem') : 'Sistem / Anonim',
            'role'       => $currentUser ? ($currentUser->role ?? 'manajer') : 'sistem',
            'action'     => $action,
            'kategori'   => $kategori,
            'deskripsi'  => $deskripsi,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => substr(Request::userAgent() ?? 'System', 0, 255),
        ]);
    }
}
