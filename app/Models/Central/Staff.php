<?php

namespace App\Models\Central;

use Laravel\Sanctum\HasApiTokens;
use App\Models\Central\Setup\Status;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordQueued;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Staff extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;
    protected $primaryKey = 'staff_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'staff_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone_number',
        'status_id',
        'password',
        'created_by',
        'updated_by',
        'last_login_at',
    ];
    protected $hidden = [
        'password',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'status_id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordQueued($token));
    }
}
