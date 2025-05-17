<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, SoftDeletes, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = [
        'avatar_url',
        'user_roles',
    ];

    //permissions
    public function get_permissions()
    {
        return $this->getPermissionNames();
    }

    //accessor untuk roles
    public function getUserRolesAttribute()
    {
        $roles = $this->roles()->get();
        $result = [];
        foreach ($roles as $role) {
            $result[] = $role->name;
        }
        return $result;
    }

    // Accessor untuk avatar URL
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && $this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return asset('assets/images/default-avatar.jpg');
    }
}
