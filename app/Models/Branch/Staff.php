<?php

namespace App\Models\Branch;

use Laravel\Sanctum\HasApiTokens;
use App\Models\Central\Setup\Status;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Staff extends Authenticatable
{
    use HasApiTokens, HasRoles;
    protected $connection = 'branch';
    protected $primaryKey = 'staff_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'staff_id',
        'first_name',
        'middle_name',
        'last_name',
        'email_address',
        'phone_number',
        'status_id',
        'password',
        'created_by',
        'updated_by'
    ];
    protected $hidden = [
        'password',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

     public function status()
    {
        return $this->belongsTo(Status::class, 'status_id', 'status_id');
    }
}
